<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 * @since     Class available since release 2.0.0
 */

namespace Hubzero\Database\Tests;

use Hubzero\Test\Database;
use Hubzero\Database\Tests\Mock\User;
use Hubzero\Database\Tests\Mock\Member;
use Hubzero\Database\Tests\Mock\Permission;
use Hubzero\Database\Tests\Mock\Post;
use Hubzero\Database\Tests\Mock\Group;
use Hubzero\Database\Tests\Mock\Project;
use Hubzero\Database\Tests\Mock\Tag;

/**
 * Base relational model tests
 */
class RelationalTest extends Database
{
	/**
	 * Sets up the tests...called prior to each test
	 *
	 * @return  void
	 **/
	public function setUp()
	{
		\Hubzero\Database\Relational::setDefaultConnection($this->getMockDriver());
	}

	/**
	 * Tests object construction and variable initialization
	 *
	 * @return  void
	 **/
	public function testConstruct()
	{
		$model = User::blank();

		$this->assertInstanceOf('\Hubzero\Database\Relational', $model, 'Model is not an instance of \Hubzero\Database\Relational');
		$this->assertEquals($model->getModelName(), 'User', 'Model should have a model name of "User"');
	}

	/**
	 * Tests to make sure a call to a helper function actually finds the function
	 *
	 * @return  void
	 **/
	public function testCallHelper()
	{
		$this->assertEquals('Test', User::one(1)->getFirstName(), 'Model should have returned a first name of "Test"');
	}

	/**
	 * Tests to make sure a call to a transformer actually finds the transformer
	 *
	 * @return  void
	 **/
	public function testCallTransformer()
	{
		$this->assertEquals('Tester', User::one(1)->nickname, 'Model should have returned a nickname of "Tester"');
	}

	/**
	 * Tests to make sure that a result can be retrieved
	 *
	 * @return  void
	 **/
	public function testOneReturnsResult()
	{
		$this->assertEquals(1, User::one(1)->id, 'Model should have returned an instance with id of 1');
	}

	/**
	 * Tests that a call for a non-existant row via oneOrFail method throws an exception
	 *
	 * @expectedException RuntimeException
	 * @return  void
	 **/
	public function testOneOrFailThrowsException()
	{
		User::oneOrFail(0);
	}

	/**
	 * Tests that a request for a non-existant row via oneOrNew method returns new model
	 *
	 * @return  void
	 **/
	public function testOneOrNewCreatesNew()
	{
		$this->assertTrue(User::oneOrNew(0)->isNew(), 'Model should have stated that it was new');
	}

	/**
	 * Tests that a belongsToOne relationship properly grabs the related side of the relationship
	 *
	 * @return  void
	 **/
	public function testBelongsToOneReturnsRelationship()
	{
		$this->assertEquals(1, Post::oneOrFail(1)->user->id, 'Model should have returned a user id of 1');
	}

	/**
	 * Tests that the belongs to one relationship can properly constrain the belongs to side
	 *
	 * @return  void
	 **/
	public function testBelongsToOneCanBeConstrained()
	{
		// Get all users that have 2 or more posts - this should return 1 result
		$posts = Post::all()->whereRelatedHas('user', function($user)
		{
			$user->whereEquals('name', 'Test User');
		})->rows();

		$this->assertCount(2, $posts, 'Model should have returned a count of 2 posts for the user by the name of "Test User"');
	}

	/**
	 * Tests that a oneToMany relationship properly grabs the many side of the relationship
	 *
	 * @return  void
	 **/
	public function testOneToManyReturnsRelationship()
	{
		$this->assertCount(2, User::oneOrFail(1)->posts, 'Model should have returned a count of 2 posts for user 1');
	}

	/**
	 * Tests that the one side of the relationship can be properly constrained by the many side
	 *
	 * @return  void
	 **/
	public function testOneToManyCanBeConstrainedByCount()
	{
		// Get all users that have 2 or more posts - this should return 1 result
		$users = User::all()->whereRelatedHasCount('posts', 2)->rows();

		$this->assertCount(1, $users, 'Model should have returned a count of 1 user with 2 or more posts');
	}

	/**
	 * Tests that a manyToMany relationship properly grabs the many side of the relationship
	 *
	 * @return  void
	 **/
	public function testManyToManyReturnsRelationship()
	{
		$this->assertCount(3, Post::oneOrFail(1)->tags, 'Model should have returned a count of 3 tags for post 1');
	}

	/**
	 * Tests that the local/left side of the m2m relationship can be properly constrained by the related/right side
	 *
	 * @return  void
	 **/
	public function testManyToManyCanBeConstrainedByCount()
	{
		$posts = Post::all()->whereRelatedHasCount('tags', 3)->rows();

		$this->assertCount(1, $posts, 'Model should have returned a count of 1 post with 3 or more tags');
	}

	/**
	 * Tests that the local/left side of the m2m relationship can be properly constrained by the related/right side
	 *
	 * @return  void
	 **/
	public function testManyToManyCanBeConstrained()
	{
		$posts = Post::all()->whereRelatedHas('tags', function($tags)
		{
			$tags->whereEquals('name', 'fun stuff');
		})->rows();

		$this->assertCount(2, $posts, 'Model should have returned a count of 2 post with the tag "fun stuff"');
	}

	/**
	 * Tests that a oneShiftsToMany relationship properly grabs the many side of the relationship
	 *
	 * @return  void
	 **/
	public function testOneShiftsToManyReturnsRelationship()
	{
		$this->assertCount(3, Group::oneOrFail(1)->members, 'Model should have returned a count of 3 members for group 1');
	}

	/**
	 * Tests that a manyShiftsToMany relationship properly grabs the many (right) side of the relationship
	 *
	 * @return  void
	 **/
	public function testManyShiftsToManyReturnsRelationship()
	{
		$this->assertCount(2, Group::oneOrFail(1)->permissions, 'Model should have returned a count of 2 permissions for group 1');
	}

	/**
	 * Tests that the local/left side of the os2m relationship can be properly constrained by the related/right side
	 *
	 * @return  void
	 **/
	public function testOneShiftsToManyCanBeConstrainedByCount()
	{
		$projects = Project::all()->whereRelatedHasCount('members', 3)->rows();

		$this->assertCount(1, $projects, 'Model should have returned a count of 1 project with 3 or more members');
	}

	/**
	 * Tests that the local/left side of the ms2m relationship can be properly constrained by the related/right side
	 *
	 * @return  void
	 **/
	public function testManyShiftsToManyCanBeConstrainedByCount()
	{
		$projects = Project::all()->whereRelatedHasCount('permissions', 2)->rows();

		$this->assertCount(1, $projects, 'Model should have returned a count of 1 project with 2 or more permissions');
	}

	/**
	 * Tests that the local/left side of the os2m relationship can be properly constrained by the related/right side
	 *
	 * @return  void
	 **/
	public function testOneShiftsToManyCanBeConstrained()
	{
		$projects = Project::all()->whereRelatedHas('members', function($members)
		{
			$members->whereEquals('user_id', 3);
		})->rows();

		$this->assertCount(1, $projects, 'Model should have returned a count of 1 project with a member whose user_id is 3');
	}

	/**
	 * Tests that the local/left side of the ms2m relationship can be properly constrained by the related/right side
	 *
	 * @return  void
	 **/
	public function testManyShiftsToManyCanBeConstrained()
	{
		$projects = Project::all()->whereRelatedHas('permissions', function($permissions)
		{
			$permissions->whereEquals('name', 'read');
		})->rows();

		$this->assertCount(2, $projects, 'Model should have returned a count of 2 project with read permissions');
	}

	/**
	 * Tests that an including call can properly preload a simple one to many relationship
	 *
	 * @return  void
	 **/
	public function testIncludingOneToManyPreloadsRelationship()
	{
		$users = User::all()->including('posts')->rows()->first();

		$this->assertNotNull($users->getRelationship('posts'), 'Model should have had a relationship named posts defined');
	}

	/**
	 * Tests that an including call can properly preload a one shifts to many relationship
	 *
	 * @return  void
	 **/
	public function testIncludingOneShiftsToManyPreloadsRelationship()
	{
		$projects = Project::all()->including('members')->rows()->first();

		$this->assertNotNull($projects->getRelationship('members'), 'Model should have had a relationship named members defined');
	}

	/**
	 * Tests that an including call can properly preload a many shifts to many relationship
	 *
	 * @return  void
	 **/
	public function testIncludingManyShiftsToManyPreloadsRelationship()
	{
		$projects = Project::all()->including('permissions')->rows()->first();

		$this->assertNotNull($projects->getRelationship('permissions'), 'Model should have had a relationship named permissions defined');
	}

	/**
	 * Tests that an including call can properly preload a simple many to many relationship
	 *
	 * @return  void
	 **/
	public function testIncludingManyToManyPreloadsRelationship()
	{
		$posts = Post::all()->including('tags')->rows()->first();

		$this->assertNotNull($posts->getRelationship('tags'), 'Model should have had a relationship named tags defined');
	}

	/**
	 * Tests that an including call can be constrained on a one to many relationship
	 *
	 * @return  void
	 **/
	public function testIncludingOneToManyCanBeConstrained()
	{
		$users = User::all()->including(['posts', function($posts)
		{
			$posts->where('content', 'LIKE', '%computer%');
		}])->rows();

		$this->assertCount(1, $users->seek(1)->posts, 'Model should have had 1 post that met the constraint');
		$this->assertCount(0, $users->seek(2)->posts, 'Model should have had 0 posts that met the constraint');
	}

	/**
	 * Tests that an including call can be constrained on a one shifts to many relationship
	 *
	 * @return  void
	 **/
	public function testIncludingOneShiftsToManyCanBeConstrained()
	{
		$projects = Project::all()->including(['members', function($posts)
		{
			$posts->whereEquals('user_id', 1);
		}])->rows();

		$this->assertCount(1, $projects->seek(1)->members, 'Model should have had 1 member that met the constraint');
		$this->assertCount(1, $projects->seek(2)->members, 'Model should have had 1 member that met the constraint');
	}

	/**
	 * Tests that an including call can be constrained on a many shifts to many relationship
	 *
	 * @return  void
	 **/
	public function testIncludingManyShiftsToManyCanBeConstrained()
	{
		$projects = Project::all()->including(['permissions', function($permissions)
		{
			$permissions->whereEquals('name', 'read');
		}])->rows();

		$this->assertCount(1, $projects->seek(1)->permissions, 'Model should have had 1 permission that met the constraint');
		$this->assertCount(0, $projects->seek(3)->permissions, 'Model should have had 0 permissions that met the constraint');
	}

	/**
	 * Tests to make sure saving a one to many relationship properly sets the associated field on the related side
	 *
	 * @return  void
	 **/
	public function testSaveOneToManyAssociatesRelated()
	{
		User::oneOrFail(1)->posts()->save(['content' => 'This is a test post']);

		$this->assertArrayHasKey('user_id', User::oneOrFail(1)->posts->last(), 'Saved item should have automatically included a user_id');
	}

	/**
	 * Tests to make sure saving a one shifts to many relationship properly sets the associated fields on the related side
	 *
	 * @return  void
	 **/
	public function testSaveOneShiftsToManyAssociatesRelated()
	{
		Project::oneOrFail(1)->members()->save(['user_id' => 2]);

		$this->assertCount(4, Project::oneOrFail(1)->members, 'Saved item should have automatically included a scope and scope_id');
	}

	/**
	 * Tests to make sure connecting a many to many properly creates the relationship
	 *
	 * @return  void
	 **/
	public function testConnectManyToManyCreatesAssociation()
	{
		// Tag post 2 with tag 3
		Post::oneOrFail(2)->tags()->connect([3]);

		$this->assertCount(2, Post::oneOrFail(2)->tags, 'Post should have had a total of 2 tags');
	}

	/**
	 * Tests to make sure connecting a many shifts to many properly creates the relationship
	 *
	 * @return  void
	 **/
	public function testConnectManyShiftsToManyCreatesAssociation()
	{
		// Tag post 2 with tag 3
		Member::oneOrFail(1)->permissions()->connect([1]);

		$this->assertCount(3, Member::oneOrFail(1)->permissions, 'Member should have had a total of 3 permissions');
	}

	/**
	 * Tests to make sure disconnecting a many to many properly destroys the relationship
	 *
	 * @return  void
	 **/
	public function testDisconnectManyToManyDestroysAssociation()
	{
		// Tag post 2 with tag 3
		Post::oneOrFail(2)->tags()->disconnect([3]);

		$this->assertCount(1, Post::oneOrFail(2)->purgeCache()->tags, 'Post should have had a total of 1 tags');
	}

	/**
	 * Tests to make sure disconnecting a many shifts to many properly destroys the relationship
	 *
	 * @return  void
	 **/
	public function testDisconnectManyShiftsToManyDestroysAssociation()
	{
		// Tag post 2 with tag 3
		Member::oneOrFail(1)->permissions()->disconnect([1]);

		$this->assertCount(2, Member::oneOrFail(1)->purgeCache()->permissions, 'Member should have had a total of 2 permissions');
	}

	/**
	 * Tests to make sure many to many save automatically connects
	 *
	 * @return  void
	 **/
	public function testManyToManySaveAutomaticallyConnects()
	{
		// Tag post 2 with tag 3
		Post::oneOrFail(2)->tags()->save(['name' => 'automatically created']);

		$this->assertCount(1, Tag::whereEquals('name', 'automatically created')->rows(), 'A tag with the name of "automatically created" should exist');
		$this->assertCount(2, Post::oneOrFail(2)->purgeCache()->tags, 'Post should have had a total of 2 tags');
	}

	/**
	 * Tests to make sure many shifts to many save automatically connects
	 *
	 * @return  void
	 **/
	public function testManyShiftsToManySaveAutomaticallyConnects()
	{
		// Tag post 2 with tag 3
		Member::oneOrFail(1)->permissions()->save(['name' => 'do awesome stuff']);

		$this->assertCount(1, Permission::whereEquals('name', 'do awesome stuff')->rows(), 'A permission with the name of "do awesome stuff" should exist');
		$this->assertCount(3, Member::oneOrFail(1)->purgeCache()->permissions, 'Member should have had a total of 3 permissions');
	}

	/**
	 * Tests to make sure connecting a many to many can also add additional fields to the intermediary table
	 *
	 * @return  void
	 **/
	public function testConnectManyToManyCanAddAdditionalFields()
	{
		// Tag post 2 with tag 4
		$now = \Date::toSql();
		Post::oneOrFail(3)->tags()->connect([1 => ['tagged' => $now]]);

		$result = Post::oneOrFail(3)->tags->seek(1);

		$this->assertFalse($result->hasAttribute('tagged'), 'Post should not have had an attributed for "tagged"');
		$this->assertArrayHasKey('tagged', (array)$result->associated, 'Post should have had an associated key of "tagged"');
		$this->assertEquals($now, $result->associated->tagged, 'Post tagged date should have equaled ' . $now);
	}

	/**
	 * Tests to make sure connecting a many shifts to many can also add additional fields to the intermediary table
	 *
	 * @return  void
	 **/
	public function testConnectManyShiftsToManyCanAddAdditionalFields()
	{
		$now = \Date::toSql();
		Group::oneOrFail(1)->permissions()->connect([3 => ['permitted' => $now]]);

		$result = Group::oneOrFail(1)->permissions->seek(3);

		$this->assertFalse($result->hasAttribute('permitted'), 'Group should not have had an attributed for "permitted"');
		$this->assertArrayHasKey('permitted', (array)$result->associated, 'Group should have had an associated key of "permitted"');
		$this->assertEquals($now, $result->associated->permitted, 'Group permitted date should have equaled ' . $now);
	}
}