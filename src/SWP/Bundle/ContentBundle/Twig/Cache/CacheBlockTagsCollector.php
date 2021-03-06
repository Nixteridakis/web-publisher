<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Twig\Cache;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Collections\ArrayCollection;

class CacheBlockTagsCollector implements CacheBlockTagsCollectorInterface
{
    private $currentCacheBlockKey = null;

    private $currentCacheBlockTags;

    private $cache;

    public function __construct(Cache $cache)
    {
        $this->currentCacheBlockTags = new ArrayCollection();
        $this->cache = $cache;
    }

    public function startNewCacheBlock(string $key): void
    {
        $this->currentCacheBlockKey = $key;
    }

    public function addTagToCurrentCacheBlock(string $tag): void
    {
        if (null !== $this->currentCacheBlockKey && !$this->currentCacheBlockTags->contains($tag)) {
            $this->currentCacheBlockTags->add($tag);
        }
    }

    public function flushCurrentCacheBlockTags(): void
    {
        if (null !== $this->currentCacheBlockKey) {
            $this->cache->save($this->currentCacheBlockKey, $this->currentCacheBlockTags);
        }

        $this->currentCacheBlockKey = null;
    }

    public function getCurrentCacheBlockTags(): array
    {
        return $this->currentCacheBlockTags->toArray();
    }

    public function getSavedCacheBlockTags(string $cacheKey)
    {
        $savedData = $this->cache->fetch($cacheKey);
        if ($savedData instanceof ArrayCollection) {
            return $savedData->toArray();
        }

        return [];
    }
}
