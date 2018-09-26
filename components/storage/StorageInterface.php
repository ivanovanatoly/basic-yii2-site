<?php
namespace app\components\storage;
/**
 *
 * Interface StorageInterface defines a set of methods to be implemented by a [[Storage]]
 *
 */
interface StorageInterface
{
    /**
     * Saves a file
     * @param string $filePath
     * @param string $name the name of the file
     * @param array $options
     * @return boolean
     */
    public function save($filePath, $name, $options = []);

    /**
     * Removes a file
     * @param string $name the name of the file to remove
     * @return boolean
     */
    public function delete($name);

    /**
     * Checks whether a file exists or not
     * @param string $name the name of the file
     * @return boolean
     */
    public function fileExists($name);

    /**
     * Returns the url of the file or empty string if the file doesn't exist.
     * @param string $name the name of the file
     * @return string
     */
    public function getUrl($name);

    /**
     * Returns the path tof the file or empty string if the file doesn't exist.
     * @param string $path path to the file
     * @return string
     */
    public function getLocalPath($path);

}
