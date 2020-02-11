<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Helpdesk\Model;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Attachment
 * @package Aheadworks\Helpdesk\Model
 */
class Attachment extends \Magento\Framework\Model\AbstractModel
{
    const TMP_PATH = 'tmp/aw_helpdesk/attachments/';

    /**
     * Filesystem
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Filesystem $filesystem
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Filesystem $filesystem,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            null,
            null,
            $data
        );
        $this->filesystem = $filesystem;
    }

    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init(\Aheadworks\Helpdesk\Model\ResourceModel\Attachment::class);
    }

    /**
     * Before save
     * @return $this
     */
    public function beforeSave()
    {
        /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $fileName = $mediaDirectory->getAbsolutePath(static::TMP_PATH) . $this->getFile();
        $fileName = str_replace(" ", "_", $fileName);
        if (file_exists($fileName)) {
            if ($this->getRemoved()) {
                $this->_dataSaveAllowed = false;
            } else {
                $this->setContent(@file_get_contents($fileName));
            }
            @unlink($fileName);
        }
        return parent::beforeSave();
    }

    /**
     * Get content length
     * @return mixed
     */
    public function getContentLength()
    {
        if ($this->getData('content_length') === null) {
            $this->setData('content_length', strlen($this->getContent()));
        }
        return $this->getData('content_length');
    }

    /**
     * Get attachment data for download
     *
     * @return array
     */
    public function getAttachmentForDownload()
    {
        $fileName = static::TMP_PATH . $this->getName();
        $directory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);

        $stream = $directory->openFile($fileName, 'w+');
        $stream->lock();
        $directory->writeFile($fileName, $this->getContent());
        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $fileName,
            'rm' => true  // can delete file after use
        ];
    }
}
