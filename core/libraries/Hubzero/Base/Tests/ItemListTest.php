<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Tests;

use Hubzero\Test\Basic;
use Hubzero\Base\ItemList;
use Hubzero\Base\Obj;

/**
 * ItemList test
 */
class ItemListTest extends Basic
{
    /**
     * A list of three, to filter down to one
     *
     * @return  object  ItemList
     **/
    protected function list()
    {
        return new ItemList(array(
            new Obj(array('name' => 'first')),
            new Obj(array('name' => 'second')),
            new Obj(array('name' => 'third')),
        ));
    }

    /**
     * Test that a filtered list can be walked
     *
     * The list reads itself by position, from zero, so a filter that keeps the
     * keys it matched on produces a list that counts what it holds and
     * iterates as though it were empty.
     *
     * @return  void
     **/
    public function testFilterKeepingALaterItemIsIterable()
    {
        $filtered = $this->list()->filter(function ($item) {
            return ($item->get('name') == 'third') ? $item : null;
        });

        $this->assertEquals(1, $filtered->count());

        $walked = array();

        foreach ($filtered as $item) {
            $walked[] = $item->get('name');
        }

        $this->assertEquals(array('third'), $walked);
    }

    /**
     * Test that the item that survived a filter can be reached
     *
     * @return  void
     **/
    public function testFilterKeepingALaterItemIsAddressable()
    {
        $filtered = $this->list()->filter(function ($item) {
            return ($item->get('name') == 'second') ? $item : null;
        });

        $this->assertNotNull($filtered->first());
        $this->assertEquals('second', $filtered->first()->get('name'));
        $this->assertEquals('second', $filtered[0]->get('name'));
    }

    /**
     * Test that a filter keeping the first item still works
     *
     * @return  void
     **/
    public function testFilterKeepingTheFirstItem()
    {
        $filtered = $this->list()->filter(function ($item) {
            return ($item->get('name') == 'first') ? $item : null;
        });

        $this->assertEquals(1, $filtered->count());
        $this->assertEquals('first', $filtered->first()->get('name'));
    }

    /**
     * Test that a filter matching nothing is empty rather than broken
     *
     * @return  void
     **/
    public function testFilterMatchingNothing()
    {
        $filtered = $this->list()->filter(function ($item) {
            return null;
        });

        $this->assertEquals(0, $filtered->count());
        $this->assertNull($filtered->first());
    }
}
