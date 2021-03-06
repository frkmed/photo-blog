<?php

namespace Lib\SiteMap\Contracts;

/**
 * Interface SiteMapItem.
 *
 * @package Lib\SiteMap\Contracts
 */
interface SiteMapItem
{
    /**
     * SiteMapItem constructor.
     *
     * @param string $location
     * @param string $lastModified
     * @param string $changeFrequency
     * @param string $priority
     */
    public function __construct(
        string $location = null,
        string $lastModified = null,
        string $changeFrequency = null,
        string $priority = null
    );

    /**
     * @param string $value
     * @return $this
     */
    public function setLocation(string $value);

    /**
     * @return string
     */
    public function getLocation() : string;

    /**
     * @return bool
     */
    public function hasLocation() : bool;

    /**
     * @param string $value
     * @return $this
     */
    public function setLastModified(string $value);

    /**
     * @return string
     */
    public function getLastModified() : string;

    /**
     * @return bool
     */
    public function hasLastModified() : bool;

    /**
     * @param string $value
     * @return $this
     */
    public function setChangeFrequency(string $value);

    /**
     * @return string
     */
    public function getChangeFrequency() : string;

    /**
     * @return bool
     */
    public function hasChangeFrequency() : bool;

    /**
     * @param string $value
     * @return $this
     */
    public function setPriority(string $value);

    /**
     * @return string
     */
    public function getPriority() : string;

    /**
     * @return bool
     */
    public function hasPriority() : bool;
}
