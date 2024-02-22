<?php

namespace App\Tests\Utils\File;

use App\Exception\FileNotExistsException;
use App\Tests\ContainerKernelTestCase;

class FileManagerTest extends ContainerKernelTestCase
{
    public function testReadFromFile(): void
    {
        $this->assertIsString($this->getFileManager()->read('README.md'));
    }

    public function testReadFromFileWhenIsNotExists(): void
    {
        $this->expectException(FileNotExistsException::class);

        $this->getFileManager()->read('file_not_exists');
    }

    public function testReadFromUrl(): void
    {
        $this->assertIsString($this->getFileManager()->read('http://localhost/bundles/apiplatform/web.png'));
    }
}
