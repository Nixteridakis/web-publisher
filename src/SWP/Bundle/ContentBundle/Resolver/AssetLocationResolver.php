<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Resolver;

use Aws\S3\S3Client;
use SWP\Bundle\ContentBundle\Model\FileInterface;

class AssetLocationResolver implements AssetLocationResolverInterface
{
    private $mainAdapter;
    private $awsBucket;
    private $awsClient;
    private $localDirectory;

    public function __construct(string $mainAdapter, string $awsBucket, S3Client $awsClient, string $localDirectory)
    {
        $this->mainAdapter = $mainAdapter;
        $this->awsBucket = $awsBucket;
        $this->localDirectory = $localDirectory;
        $this->awsClient = $awsClient;
    }

    public function getAssetUrl(FileInterface $file): string
    {
        if ('aws_adapter' === $this->mainAdapter) {
            return $this->getAwsUrl($file);
        }

        if ('local_adapter' === $this->mainAdapter) {
            return $this->getLocalUrl($file);
        }

        return '';
    }

    public function getMediaBasePath(): string
    {
        return 'swp/media';
    }

    private function getAwsUrl(FileInterface $file): string
    {
        return $this->awsClient->getObjectUrl($this->awsBucket, $file->getAssetId().'.'.$file->getFileExtension());
    }

    private function getLocalUrl(FileInterface $file): string
    {
        return  $this->localDirectory.DIRECTORY_SEPARATOR.$this->getMediaBasePath().DIRECTORY_SEPARATOR.$file->getAssetId().'.'.$file->getFileExtension();
    }
}
