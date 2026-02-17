<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer;

/**
 * com_dataviewer - Developer Reference
 * =====================================
 *
 * This component was refactored from a fully procedural codebase into namespaced
 * classes with static methods. The refactor was intentionally "light" -- function
 * bodies were preserved as-is, and the original architecture was not redesigned.
 * The goals were to bring every PHP file into a class so the PSR-4 autoloader
 * can find them, eliminate all require_once chains and phpcs:disable suppressions,
 * and replace global variables with static properties on the DvConfig classes.
 *
 *
 * Component Structure
 * -------------------
 *
 * ```
 * com_dataviewer/
 * |-- readme.php               <-- This file
 * |-- ddconvert.php            <-- Standalone CLI script (not refactored)
 * |-- dataviewer.xml           <-- Component manifest
 * |-- composer.json
 * |-- config/                  <-- JSON config templates
 * |-- migrations/              <-- Database migrations
 * |
 * |-- site/                    <-- Frontend (public-facing)
 * |   |-- Bootstrap.php        <-- Components\Dataviewer\Site\Bootstrap
 * |   |-- DvConfig.php         <-- Components\Dataviewer\Site\DvConfig
 * |   |-- Controller.php       <-- Components\Dataviewer\Site\Controller
 * |   |-- router.php           <-- SEF URL routing (procedural, unchanged)
 * |   |-- html/                <-- JS, CSS, images (static assets)
 * |   |-- Lib/
 * |   |   |-- Auth.php         <-- Components\Dataviewer\Site\Lib\Auth
 * |   |   |-- Db.php           <-- Components\Dataviewer\Site\Lib\Db
 * |   |   |-- Dl.php           <-- Components\Dataviewer\Site\Lib\Dl
 * |   |   `-- Html.php         <-- Components\Dataviewer\Site\Lib\Html
 * |   |-- Modes/
 * |   |   |-- ModeDb.php       <-- Components\Dataviewer\Site\Modes\ModeDb
 * |   |   |-- ModeDs.php       <-- Components\Dataviewer\Site\Modes\ModeDs
 * |   |   `-- ModeDsl.php      <-- Components\Dataviewer\Site\Modes\ModeDsl
 * |   |-- Filter/
 * |   |   |-- Csv.php          <-- Components\Dataviewer\Site\Filter\Csv
 * |   |   |-- Json.php         <-- Components\Dataviewer\Site\Filter\Json
 * |   |   |-- Kml.php          <-- Components\Dataviewer\Site\Filter\Kml
 * |   |   |-- Kmz.php          <-- Components\Dataviewer\Site\Filter\Kmz
 * |   |   `-- Shp.php          <-- Components\Dataviewer\Site\Filter\Shp
 * |   `-- View/
 * |       |-- File.php         <-- Components\Dataviewer\Site\View\File
 * |       |-- Gallery.php      <-- Components\Dataviewer\Site\View\Gallery
 * |       `-- Spreadsheet.php  <-- Components\Dataviewer\Site\View\Spreadsheet
 * |
 * `-- admin/                   <-- Backend (administrator)
 *     |-- Bootstrap.php        <-- Components\Dataviewer\Admin\Bootstrap
 *     |-- DvConfig.php         <-- Components\Dataviewer\Admin\DvConfig
 *     |-- Controller.php       <-- Components\Dataviewer\Admin\Controller
 *     |-- Libs/
 *     |   |-- JsonFormat.php   <-- Components\Dataviewer\Admin\Libs\JsonFormat
 *     |   |-- Messages.php     <-- Components\Dataviewer\Admin\Libs\Messages
 *     |   `-- Security.php     <-- Components\Dataviewer\Admin\Libs\Security
 *     `-- Tasks/
 *         |-- TaskList.php     <-- Components\Dataviewer\Admin\Tasks\TaskList
 *         |-- TaskConfig.php   <-- Components\Dataviewer\Admin\Tasks\TaskConfig
 *         |-- ConfigCurrent.php     <-- ...Tasks\ConfigCurrent
 *         |-- ConfigUpdate.php      <-- ...Tasks\ConfigUpdate
 *         |-- DataDefinition.php    <-- ...Tasks\DataDefinition
 *         |-- DataDefinitionNew.php <-- ...Tasks\DataDefinitionNew
 *         |-- DataDefinitionRemove.php  <-- ...Tasks\DataDefinitionRemove
 *         |-- DataDefinitionUpdate.php  <-- ...Tasks\DataDefinitionUpdate
 *         `-- DataviewList.php      <-- ...Tasks\DataviewList
 * ```
 *
 *
 * Entry Point / Bootstrap
 * -----------------------
 *
 * The framework's `Hubzero\Component\Loader` discovers Bootstrap classes
 * automatically. When a request arrives for `option=com_dataviewer`, the loader
 * instantiates the appropriate Bootstrap class and calls `start()`:
 *
 *     (new \Components\Dataviewer\Site\Bootstrap)->start();   // frontend
 *     (new \Components\Dataviewer\Admin\Bootstrap)->start();  // backend
 *
 * The `start()` method is an instance method (not static) because the framework
 * calls it via `(new $class())->start()`. It initializes config, then dispatches
 * to the Controller.
 *
 *
 * Site Request Flow
 * -----------------
 *
 * 1. `Bootstrap::start()` calls `DvConfig::init()` then `Controller::dispatch()`.
 *
 * 2. `DvConfig::init()` populates the static property `DvConfig::$dv_conf`
 *    with component parameters (display limits, processing modes, ACL rules)
 *    and defines constants: `DV_COM`, `DV_COM_PATH`, `DV_COM_HTML`, `DV_PATH_HTML`.
 *
 * 3. `Controller::dispatch()` parses the `db` request parameter to determine
 *    the database mode (`db`, `ds`, or `dsl`), then resolves the mode class
 *    dynamically:
 *
 *        $modeClass = 'Components\Dataviewer\Site\Modes\Mode' . ucfirst($mode);
 *        $modeClass::get_conf($db_id);   // loads DB connection config
 *
 *    The `task` request parameter maps to a method on Controller:
 *
 *        task=view  -->  Controller::task_view($db_id)
 *        task=data  -->  Controller::task_data($db_id)
 *
 * 4. `task_view()` loads the data definition via `$modeClass::get_dd()`,
 *    checks authorization via `self::authorize()`, then resolves a View class:
 *
 *        $viewClass = 'Components\Dataviewer\Site\View\Spreadsheet';
 *        $viewClass::render($dd);
 *
 * 5. `task_data()` generates SQL via `Lib\Db::query_gen()`, executes it via
 *    `Lib\Db::get_results()`, then passes results through a Filter class:
 *
 *        $filterClass = 'Components\Dataviewer\Site\Filter\Json';
 *        $filterClass::filter($result, $dd);
 *
 *
 * Admin Request Flow
 * ------------------
 *
 * 1. `Bootstrap::start()` calls `DvConfig::init()`, sets up document assets
 *    (CSS, JS, CSRF meta tag), calls `Controller::dispatch()`, then restores
 *    the umask.
 *
 * 2. `DvConfig::init()` sets up session-based CSRF tokens, defines constants
 *    (`DB_RID`, `DB_COM`, `DB_PATH`), loads component and database parameters,
 *    and exposes config via the static property `DvConfig::$conf`.
 *
 * 3. `Controller::dispatch()` checks authorization (group-based ACL), then
 *    resolves the task to a class via a static map:
 *
 *        $taskMap = [
 *            'list'                   => Tasks\TaskList::class,
 *            'config'                 => Tasks\TaskConfig::class,
 *            'config_current'         => Tasks\ConfigCurrent::class,
 *            'config_update'          => Tasks\ConfigUpdate::class,
 *            'data_definition'        => Tasks\DataDefinition::class,
 *            'data_definition_new'    => Tasks\DataDefinitionNew::class,
 *            'data_definition_remove' => Tasks\DataDefinitionRemove::class,
 *            'data_definition_update' => Tasks\DataDefinitionUpdate::class,
 *            'dataview_list'          => Tasks\DataviewList::class,
 *        ];
 *        $taskMap[$task]::execute();
 *
 *
 * Database Modes
 * --------------
 *
 * Three database modes control how data definitions and connections are resolved:
 *
 * - `ModeDb`  -- Standard database mode. Reads DB config from the `com_databases`
 *                plugin parameters and data definitions from the filesystem.
 *
 * - `ModeDs`  -- Dataset mode. Similar to ModeDb but works with dataset-specific
 *                configurations and supports additional access control.
 *
 * - `ModeDsl` -- Dataset Link mode. Used by com_projects for project-linked
 *                databases. Reads connection info from the `projects/databases`
 *                plugin parameters.
 *
 * Each mode class implements these static methods:
 *
 * - `get_conf($db_id)`  -- Populates `DvConfig::$dv_conf['db']` with connection parameters.
 * - `get_dd($db_id)`    -- Returns a data definition array (`$dd`) describing
 *                          the table, columns, labels, joins, and display options.
 * - `pathway($dd)`      -- Sets up the breadcrumb pathway for the current view.
 *
 *
 * Output Filters
 * --------------
 *
 * Filters transform query results into output formats. Each filter class has a
 * static `filter($res, &$dd)` method:
 *
 * - `Filter\Json`  -- Default. Transforms results into JSON for the DataTables
 *                     jQuery plugin. Handles column linking, operational
 *                     modifications (coloring, formatting), and download hashes.
 *                     (~846 lines, the largest single file.)
 *
 * - `Filter\Csv`   -- Streams results as a CSV file download. Includes column
 *                     metadata and a DATASTART separator.
 *
 * - `Filter\Kml`   -- Generates KML (Google Earth) output with placemarks from
 *                     lat/lng columns. Supports DMS coordinate conversion.
 *
 * - `Filter\Kmz`   -- Same as KML but compressed into a ZIP archive (.kmz).
 *
 * - `Filter\Shp`   -- Generates ESRI Shapefile output using ogr2ogr. Creates a
 *                     temporary VRT/CSV, converts to .shp, and streams as .zip.
 *
 *
 * View Renderers
 * --------------
 *
 * Views handle the HTML page rendering. Each has a static `render()` method:
 *
 * - `View\Spreadsheet` -- The primary view. Renders an interactive DataTables
 *                         spreadsheet with filtering, sorting, column toggling,
 *                         and chart support. (~586 lines.)
 *
 * - `View\File`        -- Handles file downloads and multi-file zip streaming
 *                         using session-stored download hashes.
 *
 * - `View\Gallery`     -- Renders an image gallery view.
 *
 *
 * Library Classes
 * ---------------
 *
 * - `Lib\Db`   -- Database query layer. Builds SQL queries from data definitions,
 *                 handles pagination, sorting, searching, joins, and executes
 *                 queries via `\Hubzero\Database\Driver`. (~760 lines.)
 *
 *                 Key methods:
 *                 - `get_db($db_id)` -- Returns a `\Hubzero\Database\Driver` instance
 *                 - `query_gen(&$dd)` -- Builds SELECT SQL from the $dd array
 *                 - `get_results($sql, &$dd)` -- Executes query, returns result set
 *                   (result `$res['data']` is an array of associative arrays)
 *                 - `query_gen_total(&$dd)` -- Builds COUNT query for pagination
 *
 * - `Lib\Dl`   -- File download helpers. Manages session-based download hashes
 *                 for secure file streaming.
 *
 * - `Lib\Html` -- Asset loading. Adds JS/CSS files to the document, resolving
 *                 paths relative to the component's `html/` directory.
 *
 * - `Lib\Auth` -- ACL checking for site-side requests. Validates against
 *                 allowed users and groups from component parameters.
 *
 *
 * Shared State (Static Properties)
 * --------------------------------
 *
 * Component state is stored as public static properties on the DvConfig classes.
 * The class is named `DvConfig` (not `Config`) to avoid confusion with HubZero's
 * `\Config` facade (`Hubzero\Facades\Config`).
 *
 * Site-side (`Components\Dataviewer\Site\DvConfig`):
 * - `DvConfig::$dv_conf`   -- Primary configuration array (settings, ACL, DB config)
 * - `DvConfig::$com_name`  -- Component name without `com_` prefix ("dataviewer")
 * - `DvConfig::$html_path` -- Relative path to the html/ assets directory
 *
 * Admin-side (`Components\Dataviewer\Admin\DvConfig`):
 * - `DvConfig::$conf`      -- Primary admin configuration array
 * - `DvConfig::$com_name`  -- Component name without `com_` prefix
 *
 * Site-side constants (defined in DvConfig::init()):
 * - `DV_COM`       -- Component name ("dataviewer")
 * - `DV_COM_PATH`  -- Relative component path from document root
 * - `DV_COM_HTML`  -- Relative path to html/ assets
 * - `DV_PATH_HTML` -- Absolute filesystem path to html/ assets
 *
 * Admin-side constants (defined in DvConfig::init()):
 * - `DB_RID`   -- Session-based CSRF token
 * - `DB_COM`   -- Component name
 * - `DB_PATH`  -- Relative component path from document root
 *
 *
 * Cross-Component Usage
 * ---------------------
 *
 * `com_publications` uses com_dataviewer's site classes directly to generate
 * CSV exports of project-linked databases:
 *
 *     \Components\Dataviewer\Site\DvConfig::init();
 *     $dd = \Components\Dataviewer\Site\Modes\ModeDsl::get_dd(null, $db_name, $version);
 *     $sql = \Components\Dataviewer\Site\Lib\Db::query_gen($dd);
 *     $result = \Components\Dataviewer\Site\Lib\Db::get_results($sql, $dd);
 *     \Components\Dataviewer\Site\Filter\Csv::filter($result, $dd, true);
 *
 * This cross-component dependency lives in:
 * `com_publications/models/attachments/data.php` (Data::getCsvData)
 *
 *
 * Important Notes
 * ---------------
 *
 * - This component uses `\Hubzero\Database\Driver::getInstance()` for database
 *   connections with raw SQL via `setQuery()`/`loadAssocList()`. The
 *   `Lib\Db::get_db()` method creates Driver instances using credentials from
 *   the data definition config files. Filter classes receive `$res['data']` as
 *   a plain PHP array of associative arrays (not a result cursor).
 *
 * - Several files contain `system()` and `exec()` calls for ogr2ogr (Shapefile
 *   generation), git operations (data definition versioning), and zip commands.
 *
 * - The `ddconvert.php` at the component root is a standalone CLI script
 *   (#!/usr/bin/php) used to convert PHP data definitions to JSON. It was
 *   intentionally NOT refactored into a class.
 *
 * - The `router.php` file remains procedural as it follows the HubZero SEF
 *   router convention expected by the framework.
 *
 * - All facade calls (Request, App, User, Component, etc.) are prefixed with
 *   `\` backslash since these classes live in the global namespace and are
 *   called from within component namespaces.
 *
 *
 * Refactoring History
 * -------------------
 *
 * The original codebase was fully procedural with ~30 standalone functions
 * spread across include files. Multiple files defined identically-named
 * functions (e.g., `filter()` in 5 files, `get_conf()` in 4 files) relying
 * on the fact that only one was loaded per request via dynamic require_once.
 *
 * The refactoring converted these into namespaced classes where each function
 * became a public static method. The class structure naturally resolved the
 * function-name collisions since `Filter\Csv::filter()` and
 * `Filter\Json::filter()` are distinct.
 *
 * File and directory renames for autoloader compatibility:
 * - `lib/` -> `Lib/`, `modes/` -> `Modes/`, `filter/` -> `Filter/`, etc.
 * - `db.php` -> `Db.php`, `mode_db.php` -> `ModeDb.php`, etc.
 * - `dv_config.php` -> `DvConfig.php`, `controller.php` -> `Controller.php`
 * - `libs/lib_json.php` -> `Libs/JsonFormat.php`
 * - `tasks/list.php` -> `Tasks/TaskList.php` (avoiding PHP reserved word)
 *
 * Internal function calls were updated to use static method syntax:
 * - `get_db($id)` -> `Lib\Db::get_db($id)` or `static::get_db($id)`
 * - `dv_add_script($s)` -> `Lib\Html::dv_add_script($s)`
 * - `filter($r, $d)` -> `$filterClass::filter($r, $d)` (dynamic resolution)
 *
 * The `view()` function in View classes was renamed to `render()` to avoid
 * conflicts with potential PHP reserved words and improve clarity.
 */
class Readme
{
}
