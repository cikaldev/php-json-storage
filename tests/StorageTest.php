<?php
require __DIR__ . '/../src/Storage/Storage.php';
define('TESTING_STORE', __DIR__ . DIRECTORY_SEPARATOR . 'test.json');

use PHPUnit\Framework\TestCase;
use Cikal\Storage\Storage;

class StorageTest extends TestCase
{
    protected $store;
    protected $single_dummy;
    protected $batch_dummy;

    public function __construct()
    {
        // dummy data
        $this->single_dummy = array(
            'label' => 'hello world',
            'message' => 'let me test the library before people use it in their projects',
        );

        $this->batch_dummy = array(
            array('label' => 'test 1','msg' => 'Lorem ipsum dolor sit amet.','time' => date('Y-m-d H:i:s')),
            array('label' => 'test 2','msg' => 'Fugiat expedita eligendi unde pariatur.','time' => date('Y-m-d H:i:s')),
            array('label' => 'test 3','msg' => 'Excepturi non, fugiat alias repellat.','time' => date('Y-m-d H:i:s')),
            array('label' => 'test 4','msg' => 'Incidunt odit deserunt, velit fugit!','time' => date('Y-m-d H:i:s')),
            array('label' => 'test 5','msg' => 'Nostrum delectus rem, quas doloribus!','time' => date('Y-m-d H:i:s')),
            array('label' => 'test 6','msg' => 'At nobis tempora voluptates numquam.','time' => date('Y-m-d H:i:s')),
            array('label' => 'test 7','msg' => 'Cumque praesentium vel inventore corporis.','time' => date('Y-m-d H:i:s')),
            array('label' => 'test 8','msg' => 'Reiciendis debitis modi perspiciatis, ullam.','time' => date('Y-m-d H:i:s')),
            array('label' => 'test 9','msg' => 'Laudantium laborum magni odio aliquid.','time' => date('Y-m-d H:i:s')),
            array('label' => 'test 10','msg' => 'Aliquam labore mollitia suscipit corporis.','time' => date('Y-m-d H:i:s')),
        );
    }

    public function testInitialClassInstance()
    {
        $this->assertInstanceOf(Storage::class, new Storage(TESTING_STORE));
    }

    public function testCreateNewStorageFromConstructor()
    {
        // clean out previous test
        unlink(TESTING_STORE);

        // create new instance
        $store = new Storage(TESTING_STORE);

        // boolean test storage file creating, result must be === true
        $this->assertTrue(file_exists(TESTING_STORE));
    }

    public function testInsertingNewDataToStorage()
    {
        // clean out previous test
        unlink(TESTING_STORE);

        // create new instance
        $store = new Storage(TESTING_STORE);

        // insert the data into store
        $store->insert($this->single_dummy);

        // fetch the data and count the result
        // since we insert only one new data, so the test result must be === 1
        $this->assertEquals(1, count($store->read()));
    }

    public function testUpdatingData()
    {
        // create new instance (use existing data)
        $store = new Storage(TESTING_STORE);

        // get id from our first item
        $id = $store->read()[0]['_id'];

        // new dummy data
        $new_dummy = array(
            'label' => 'test update',
            'message' => 'let me update this old data',
        );

        // do update our data
        $store->update($id, $new_dummy);

        // fetch single item from data by id
        $item = $store->read($id);

        // testing data
        $test = array($item['label'], $item['message']);

        // here we make a test that count diff values from data between 2 array_values
        $diff_count = count(array_diff(array_values($new_dummy), $test));
        $this->assertEquals(0, $diff_count);
    }

    public function testDestroyData()
    {
        // create new instance (use existing data)
        $store = new Storage(TESTING_STORE);

        // get id from our first item
        $id = $store->read()[0]['_id'];

        // destroy the data by id
        $store->destroy($id);

        // get the rest of data after destroy
        $rest_of_items = $store->read();

        // since our data is only one (no data left) the results must be === 0
        $this->assertEquals(0, count($rest_of_items));
    }

    public function testCreateBatchData()
    {
        // clean out previous test
        unlink(TESTING_STORE);

        // create new instance
        $store = new Storage(TESTING_STORE);

        // insert dummy batch data at one shot
        $store->insertBatch($this->batch_dummy);

        // let assign our new batch data into variable
        $items = $store->read();

        // here we make a test that count amount of data available
        // since our batch data is 10, the results must be === 10
        $this->assertEquals(10, count($items));
    }

    public function testReadAllDataAndCompareTheValueFromPreviousTest()
    {
        // create new instance (use existing data)
        $store = new Storage(TESTING_STORE);

        // let assign our data result into variable
        $items = $store->read();

        // mapping msg value from items
        $item_messages = array_map(function (&$val) {
            return $val['msg'];
        }, $items);

        // mapping msg value from batch dummy
        $batch_dummy = array_map(function (&$val) {
            return $val['msg'];
        }, $this->batch_dummy);

        // here we make a test that count diff values from data between 2 array
        $diff_count = count(array_diff($item_messages, $batch_dummy));
        $this->assertEquals(0, $diff_count);
    }

    public function testPurgeWholeData()
    {
        // create new instance (use existing data)
        $store = new Storage(TESTING_STORE);

        // do purging data
        $store->purge();

        // the results must be === 0
        $this->assertEquals(0, count($store->read()));
    }

    public function testRemoveDummyTestFileAfterDoneTest()
    {
        // clean out previous test
        unlink(TESTING_STORE);

        // boolean result must be === false
        $this->assertFalse(file_exists(TESTING_STORE));
    }
}
