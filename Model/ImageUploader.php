<?php
declare(strict_types=1);

namespace Ezzar\VisualLayers\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;

class ImageUploader
{
    private const MEDIA_SUBDIR = 'visual_layers';

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly UploaderFactory $uploaderFactory
    ) {
    }

    public function upload(string $fileId): ?string
    {
        if (empty($_FILES[$fileId]['name'])) {
            return null;
        }

        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'webp', 'svg']);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(false);

        // Store a relative media path so environments can use different base media URLs.
        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $result = $uploader->save($mediaDirectory->getAbsolutePath(self::MEDIA_SUBDIR));

        if (!isset($result['file'])) {
            throw new LocalizedException(__('The layered image could not be uploaded.'));
        }

        return self::MEDIA_SUBDIR . '/' . ltrim((string) $result['file'], '/');
    }
}
