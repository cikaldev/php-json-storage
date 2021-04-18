<?php
/**
 * @source  https://github.com/cikaldev/php-json-storage
 * @author Ian Cikal <cikaldev@gmail.com>
 * @copyright Copyright (c), 2021 Ian Cikal
 * @license MIT public license
 */
namespace Cikal\Storage;

/**
 * Class Storage
 *
 * Just another helper file for doing CRUD on flat json file -
 * as your project storage.
 *
 * No need dependencies, just make sure the source destination
 * has writable permission before using this file as part of -
 * your great project.
 */
class Storage
{
    /**
     * $source Filename
     * @var null|string filename
     */
    protected $source = null;

    /**
     * __construct : Class constructor
     * @param string $source filename
     */
    public function __construct($source)
    {
        $this->source = $source;
        $this->store($this->source);
    }

    /**
     * insert : Insert one data to storage
     * @param  array  $data
     * @return string _id
     */
    public function insert($data)
    {
        $handler = json_decode(file_get_contents($this->source), true);
        $data['_id'] = $this->makeId();
        array_push($handler, $data);
        file_put_contents($this->source, json_encode($handler));
    }

    /**
     * insertBatch : Insert batch data once to storage
     * @param  array  $data
     * @return void
     */
    public function insertBatch($data)
    {
        $handler = json_decode(file_get_contents($this->source), true);
        foreach ($data as $val) {
            $val['_id'] = $this->makeId();
            array_push($handler, $val);
        }
        file_put_contents($this->source, json_encode($handler));
    }

    /**
     * read : Storage data
     *
     * If $id is not empty, return single results otherwise -
     * will return whole data from Storage.
     *
     * @param  string $id data id
     * @return array
     */
    public function read($id = null)
    {
        $handler = json_decode(file_get_contents($this->source), true);
        if ($id !== null) {
            $output = array();
            foreach ($handler as $key => $val) {
                if ($val['_id'] === $id) {
                    $output = $val;

                    break;
                }
            }

            return $output;
        }

        return $handler;
    }

    /**
     * update : Update existing data by _id
     * @param  string $id
     * @param  array  $data
     * @return void
     */
    public function update($id, $data)
    {
        $handler = json_decode(file_get_contents($this->source), true);
        foreach ($handler as $key => $val) {
            if ($val['_id'] === $id) {
                $handler[$key] = array_merge($val, $data);

                break;
            }
        }
        file_put_contents($this->source, json_encode($handler));
    }

    /**
     * destroy : Remove data from storage by _id
     * @param  string $id data id
     * @return void
     */
    public function destroy($id)
    {
        $handler = json_decode(file_get_contents($this->source), true);
        foreach ($handler as $key => $val) {
            if ($val['_id'] === $id) {
                unset($handler[$key]);

                break;
            }
        }
        file_put_contents($this->source, json_encode(array_values($handler)));
    }

    public function purge()
    {
        file_put_contents($this->source, json_encode(array()));
    }

    /**
     * store : Create new json file for storage the data
     * @param  string $source filename
     */
    private function store($source)
    {
        if (!file_exists($source)) {
            file_put_contents($source, json_encode(array()));
        }
    }

    /**
     * makeId : Generate unique id (uuid)
     * @return string Formatted by "-" each 6 char (length 20 char)
     */
    private function makeId()
    {
        $str = PHP_MAJOR_VERSION < 7 ? openssl_random_pseudo_bytes(16) : random_bytes(16);
        // Set version to 0100
        $str[6] = chr(ord($str[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $str[8] = chr(ord($str[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($str), 4));
    }
}

/* End of file Storage.php */
/* Location: ./src/Storage/Storage.php */
