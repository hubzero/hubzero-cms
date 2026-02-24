<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

/**
 * Model Factory for generating test data
 *
 * Factories provide a convenient way to generate model instances for testing.
 * Each factory defines default attribute values and optional states that can
 * be applied to create variations.
 *
 * ## Creating a Factory
 *
 * Extend this class and implement the `definition()` method:
 *
 * ```php
 * class ArticleFactory extends Factory
 * {
 *     protected $model = Article::class;
 *
 *     public function definition()
 *     {
 *         return [
 *             'title' => 'Article ' . self::sequence(),
 *             'content' => self::paragraph(),
 *             'status' => 'draft',
 *             'created_by' => 1,
 *         ];
 *     }
 *
 *     // Optional: define states for variations
 *     public function published()
 *     {
 *         return $this->state(['status' => 'published']);
 *     }
 * }
 * ```
 *
 * ## Basic Usage
 *
 * ```php
 * // Create a single model (persisted to database)
 * $article = Article::factory()->create();
 *
 * // Create without persisting (in-memory only)
 * $article = Article::factory()->make();
 *
 * // Create multiple models
 * $articles = Article::factory()->count(5)->create();
 *
 * // Override attributes
 * $article = Article::factory()->create(['title' => 'Custom Title']);
 *
 * // Use a state
 * $article = Article::factory()->published()->create();
 *
 * // Chain multiple states
 * $article = Article::factory()->published()->featured()->create();
 * ```
 *
 * ## Relationships
 *
 * ```php
 * // Create article with 3 comments
 * $article = Article::factory()
 *     ->has(Comment::factory()->count(3))
 *     ->create();
 *
 * // Create comment for a specific article
 * $comment = Comment::factory()
 *     ->for($article)
 *     ->create();
 * ```
 *
 * ## Built-in Generators (No Faker Required)
 *
 * | Method                | Description                          |
 * |-----------------------|--------------------------------------|
 * | sequence()            | Auto-incrementing integer            |
 * | uuid()                | Random UUID v4                       |
 * | randomInt($min, $max) | Random integer in range              |
 * | randomFloat($min,$max)| Random float in range                |
 * | randomElement($arr)   | Random element from array            |
 * | boolean($chance)      | Random boolean (chance 0-100)        |
 * | timestamp()           | Current datetime string              |
 * | pastDate($days)       | Random date within past N days       |
 * | futureDate($days)     | Random date within next N days       |
 * | sentence($words)      | Lorem ipsum sentence                 |
 * | paragraph($sentences) | Lorem ipsum paragraph                |
 * | words($count)         | Array of lorem ipsum words           |
 * | slug($words)          | URL-friendly slug                    |
 * | email($name)          | Fake email address                   |
 */
abstract class Factory
{
    /**
     * The model class this factory creates
     *
     * @var string
     */
    protected $model;

    /**
     * Number of models to create
     *
     * @var int
     */
    protected $count = 1;

    /**
     * State modifications to apply
     *
     * @var array
     */
    protected $states = [];

    /**
     * Relationships to create with the model
     *
     * @var array
     */
    protected $has = [];

    /**
     * Parent model to associate with
     *
     * @var array
     */
    protected $for = [];

    /**
     * Callbacks to run after creating
     *
     * @var array
     */
    protected $afterCreating = [];

    /**
     * Callbacks to run after making
     *
     * @var array
     */
    protected $afterMaking = [];

    /**
     * Sequence counter for unique values
     *
     * @var int
     */
    protected static $sequenceCounter = 1;

    /**
     * Lorem ipsum words for text generation
     *
     * @var array
     */
    protected static $loremWords = [
        'lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur', 'adipiscing',
        'elit', 'sed', 'do', 'eiusmod', 'tempor', 'incididunt', 'ut', 'labore',
        'et', 'dolore', 'magna', 'aliqua', 'enim', 'ad', 'minim', 'veniam',
        'quis', 'nostrud', 'exercitation', 'ullamco', 'laboris', 'nisi',
        'aliquip', 'ex', 'ea', 'commodo', 'consequat', 'duis', 'aute', 'irure',
        'in', 'reprehenderit', 'voluptate', 'velit', 'esse', 'cillum', 'fugiat',
        'nulla', 'pariatur', 'excepteur', 'sint', 'occaecat', 'cupidatat',
        'non', 'proident', 'sunt', 'culpa', 'qui', 'officia', 'deserunt',
        'mollit', 'anim', 'id', 'est', 'laborum'
    ];

    // =========================================================================
    // Factory Instantiation
    // =========================================================================

    /**
     * Create a new factory instance
     *
     * @return static
     */
    public static function new()
    {
        return new static(); // @phpstan-ignore new.static
    }

    /**
     * Define the model's default state
     *
     * Override this method to provide default attribute values.
     *
     * @return array
     */
    abstract public function definition();

    /**
     * Get the model class this factory creates
     *
     * @return string
     */
    public function modelClass()
    {
        return $this->model;
    }

    // =========================================================================
    // Factory Configuration
    // =========================================================================

    /**
     * Set the number of models to create
     *
     * @param   int  $count
     * @return  $this
     */
    public function count(int $count)
    {
        $this->count = $count;
        return $this;
    }

    /**
     * Add state modifications
     *
     * States allow you to define discrete modifications that can be applied
     * to your factories. Each state is an array of attributes that will be
     * merged with the definition.
     *
     * @param   array|callable  $state  Attributes to merge or callable returning attributes
     * @return  $this
     */
    public function state($state)
    {
        $this->states[] = $state;
        return $this;
    }

    /**
     * Define a has-many relationship to create
     *
     * @param   Factory  $factory       The related factory
     * @param   string   $relationship  The relationship method name (optional)
     * @return  $this
     */
    public function has(Factory $factory, string $relationship = null)
    {
        $this->has[] = [
            'factory' => $factory,
            'relationship' => $relationship,
        ];
        return $this;
    }

    /**
     * Define a belongs-to relationship
     *
     * @param   Relational  $model         The parent model instance
     * @param   string      $relationship  The relationship method name (optional)
     * @return  $this
     */
    public function for(Relational $model, string $relationship = null)
    {
        $this->for[] = [
            'model' => $model,
            'relationship' => $relationship,
        ];
        return $this;
    }

    /**
     * Register a callback to run after creating
     *
     * @param   callable  $callback
     * @return  $this
     */
    public function afterCreating(callable $callback)
    {
        $this->afterCreating[] = $callback;
        return $this;
    }

    /**
     * Register a callback to run after making
     *
     * @param   callable  $callback
     * @return  $this
     */
    public function afterMaking(callable $callback)
    {
        $this->afterMaking[] = $callback;
        return $this;
    }

    // =========================================================================
    // Model Creation
    // =========================================================================

    /**
     * Create model instance(s) without persisting to database
     *
     * @param   array  $attributes  Additional attributes to set
     * @return  Relational|Rows  Single model or collection
     */
    public function make(array $attributes = [])
    {
        $results = [];

        for ($i = 0; $i < $this->count; $i++) {
            $model = $this->makeInstance($attributes);
            $results[] = $model;
        }

        // Run afterMaking callbacks
        foreach ($results as $model) {
            foreach ($this->afterMaking as $callback) {
                $callback($model);
            }
        }

        return $this->count === 1 ? $results[0] : new Rows($results);
    }

    /**
     * Create and persist model instance(s) to database
     *
     * @param   array  $attributes  Additional attributes to set
     * @return  Relational|Rows  Single model or collection
     */
    public function create(array $attributes = [])
    {
        $results = [];

        for ($i = 0; $i < $this->count; $i++) {
            $model = $this->makeInstance($attributes);
            $model->save();
            $results[] = $model;
        }

        // Create has-many relationships
        foreach ($this->has as $hasRelation) {
            foreach ($results as $model) {
                $foreignKey = $this->guessForeignKey($model);
                $hasRelation['factory']->create([
                    $foreignKey => $model->get($model->getPrimaryKey()),
                ]);
            }
        }

        // Run afterCreating callbacks
        foreach ($results as $model) {
            foreach ($this->afterCreating as $callback) {
                $callback($model);
            }
        }

        return $this->count === 1 ? $results[0] : new Rows($results);
    }

    /**
     * Create a single model instance
     *
     * @param   array  $attributes  Additional attributes
     * @return  Relational
     */
    protected function makeInstance(array $attributes = [])
    {
        $modelClass = $this->model;

        // Get definition
        $definition = $this->definition();

        // Apply states
        foreach ($this->states as $state) {
            if (is_callable($state)) {
                $state = $state($definition);
            }
            $definition = array_merge($definition, $state);
        }

        // Apply belongs-to relationships (set foreign keys)
        foreach ($this->for as $forRelation) {
            $parent = $forRelation['model'];
            $foreignKey = $this->guessForeignKey($parent);
            $definition[$foreignKey] = $parent->get($parent->getPrimaryKey());
        }

        // Merge with provided attributes (highest priority)
        $definition = array_merge($definition, $attributes);

        // Create model instance
        $model = $modelClass::blank();

        foreach ($definition as $key => $value) {
            // Resolve callables
            if (is_callable($value) && !is_string($value)) {
                $value = $value($definition);
            }
            $model->set($key, $value);
        }

        return $model;
    }

    /**
     * Guess the foreign key name for a model
     *
     * @param   Relational  $model
     * @return  string
     */
    protected function guessForeignKey(Relational $model)
    {
        // Get the model's table name and make a foreign key from it
        $table = $model->getTableName();

        // Remove common prefixes
        $table = preg_replace('/^#__/', '', $table);
        $table = preg_replace('/^jos_/', '', $table);

        // Singularize (simple version - remove trailing 's')
        if (substr($table, -1) === 's') {
            $table = substr($table, 0, -1);
        }

        return $table . '_id';
    }

    // =========================================================================
    // Built-in Generators (No Faker Required)
    // =========================================================================

    /**
     * Get an auto-incrementing sequence number
     *
     * @return  int
     */
    public static function sequence()
    {
        return self::$sequenceCounter++;
    }

    /**
     * Reset the sequence counter
     *
     * @param   int  $start  Starting value (default 1)
     * @return  void
     */
    public static function resetSequence(int $start = 1)
    {
        self::$sequenceCounter = $start;
    }

    /**
     * Flush static factory runtime state.
     *
     * @param   array  $options  Supported keys:
     *                           - reset_sequence_to (int, default 1)
     * @return  void
     */
    public static function flush(array $options = []): void
    {
        $start = (int) ($options['reset_sequence_to'] ?? 1);
        self::resetSequence($start);
    }

    /**
     * Generate a UUID v4
     *
     * @return  string
     */
    public static function uuid()
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Generate a random integer in range
     *
     * @param   int  $min
     * @param   int  $max
     * @return  int
     */
    public static function randomInt(int $min = 0, int $max = PHP_INT_MAX)
    {
        return random_int($min, $max);
    }

    /**
     * Generate a random float in range
     *
     * @param   float  $min
     * @param   float  $max
     * @param   int    $decimals
     * @return  float
     */
    public static function randomFloat(float $min = 0, float $max = 1, int $decimals = 2)
    {
        $scale = pow(10, $decimals);
        return random_int($min * $scale, $max * $scale) / $scale;
    }

    /**
     * Get a random element from an array
     *
     * @param   array  $array
     * @return  mixed
     */
    public static function randomElement(array $array)
    {
        return $array[array_rand($array)];
    }

    /**
     * Generate a random boolean
     *
     * @param   int  $chanceOfTrue  Percentage chance of true (0-100)
     * @return  bool
     */
    public static function boolean(int $chanceOfTrue = 50)
    {
        return random_int(1, 100) <= $chanceOfTrue;
    }

    /**
     * Get current timestamp as SQL datetime
     *
     * @return  string
     */
    public static function timestamp()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * Generate a random date in the past
     *
     * @param   int  $maxDaysAgo
     * @return  string
     */
    public static function pastDate(int $maxDaysAgo = 30)
    {
        $daysAgo = random_int(1, $maxDaysAgo);
        return date('Y-m-d H:i:s', strtotime("-{$daysAgo} days"));
    }

    /**
     * Generate a random date in the future
     *
     * @param   int  $maxDaysAhead
     * @return  string
     */
    public static function futureDate(int $maxDaysAhead = 30)
    {
        $daysAhead = random_int(1, $maxDaysAhead);
        return date('Y-m-d H:i:s', strtotime("+{$daysAhead} days"));
    }

    /**
     * Generate lorem ipsum words
     *
     * @param   int   $count
     * @param   bool  $asArray  Return as array or string
     * @return  array|string
     */
    public static function words(int $count = 3, bool $asArray = false)
    {
        $words = [];
        for ($i = 0; $i < $count; $i++) {
            $words[] = self::$loremWords[array_rand(self::$loremWords)];
        }

        return $asArray ? $words : implode(' ', $words);
    }

    /**
     * Generate a lorem ipsum sentence
     *
     * @param   int  $wordCount
     * @return  string
     */
    public static function sentence(int $wordCount = 6)
    {
        $words = self::words($wordCount);
        return ucfirst($words) . '.';
    }

    /**
     * Generate a lorem ipsum paragraph
     *
     * @param   int  $sentenceCount
     * @return  string
     */
    public static function paragraph(int $sentenceCount = 3)
    {
        $sentences = [];
        for ($i = 0; $i < $sentenceCount; $i++) {
            $sentences[] = self::sentence(random_int(5, 12));
        }
        return implode(' ', $sentences);
    }

    /**
     * Generate multiple paragraphs
     *
     * @param   int   $count
     * @param   bool  $asArray
     * @return  array|string
     */
    public static function paragraphs(int $count = 3, bool $asArray = false)
    {
        $paragraphs = [];
        for ($i = 0; $i < $count; $i++) {
            $paragraphs[] = self::paragraph(random_int(3, 6));
        }

        return $asArray ? $paragraphs : implode("\n\n", $paragraphs);
    }

    /**
     * Generate a URL-friendly slug
     *
     * @param   int  $wordCount
     * @return  string
     */
    public static function slug(int $wordCount = 3)
    {
        $words = self::words($wordCount, true);
        return implode('-', $words);
    }

    /**
     * Generate a fake email address
     *
     * @param   string|null  $name  Optional name to base email on
     * @return  string
     */
    public static function email(string $name = null)
    {
        if ($name === null) {
            $name = self::words(1) . self::sequence();
        }

        $name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));
        $domains = ['example.com', 'test.com', 'sample.org', 'demo.net'];

        return $name . '@' . self::randomElement($domains);
    }

    /**
     * Generate a random hex color
     *
     * @param   bool  $withHash
     * @return  string
     */
    public static function hexColor(bool $withHash = true)
    {
        $color = sprintf('%06x', random_int(0, 0xFFFFFF));
        return $withHash ? '#' . $color : $color;
    }

    /**
     * Generate a random IP address
     *
     * @return  string
     */
    public static function ipAddress()
    {
        return implode('.', [
            random_int(1, 255),
            random_int(0, 255),
            random_int(0, 255),
            random_int(1, 254),
        ]);
    }

    /**
     * Generate a random URL
     *
     * @return  string
     */
    public static function url()
    {
        $protocols = ['http', 'https'];
        $domains = ['example.com', 'test.com', 'sample.org', 'demo.net'];

        return self::randomElement($protocols) . '://'
            . self::randomElement($domains) . '/'
            . self::slug(random_int(1, 3));
    }
}
