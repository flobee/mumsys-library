<?php


/**
 * Test class for Mumsys_FileSystem.
 */
class Mumsys_FileSystemTest
    extends Mumsys_Unittest_Testcase
{
    /**
     * @var Mumsys_FileSystem
     */
    protected $_object;
    protected $_testdirs;
    protected $_testsDir;


    protected function setUp()
    {
        $this->_version = '3.0.7';
        $this->_versions = array(
            'Mumsys_FileSystem' => $this->_version,
            'Mumsys_FileSystem_Common_Abstract' => '3.1.0',
        );

        $this->_testsDir = realpath( dirname( __FILE__ ) . '/../' );
        $this->_testdirs = array(
            'rm1' => $this->_testsDir . '/tmp/unittest-mkdir/mkdirs/testfile',
            'rm2' => $this->_testsDir . '/tmp/unittest-mkdir/testfile',
            'dir' => $this->_testsDir . '/tmp/unittest-mkdir',
            'dirs' => $this->_testsDir . '/tmp/unittest-mkdir/mkdirs',
            'file' => $this->_testsDir . '/tmp/unittest',
            'file2' => $this->_testsDir . '/tmp/unittest_2',
            'invalid' => '/root/goes/here',
            'rm3' => $this->_testsDir . '/tmp/mkdirs',
            'rm4' => $this->_testsDir . '/tmp/unittest-mkdir.lnk',
        );
        touch( $this->_testdirs['file'] );

        $this->_object = new Mumsys_FileSystem();
    }


    protected function tearDown()
    {
        foreach ( $this->_testdirs as $dir ) {
            if ( !@rmdir( $dir ) ) {
                @unlink( $dir );
            }
        }
    }


    /**
     * @covers Mumsys_FileSystem_Common_Abstract::extGet
     */
    public function testextGet()
    {
        $actual1 = $this->_object->extGet( 'filename.ext' );
        $actual2 = $this->_object->extGet( 'info' );

        $this->assertingEquals( 'ext', $actual1 );
        $this->assertingEquals( '', $actual2 );
    }


    /**
     * @covers Mumsys_FileSystem_Common_Abstract::nameGet
     */
    public function testnameGet()
    {
        $actual1 = $this->_object->nameGet( 'filename.ext' );
        $actual2 = $this->_object->nameGet( '/some/file/at/filename' );

        $this->assertingEquals( 'filename', $actual1 );
        $this->assertingEquals( 'filename', $actual2 );
    }


    /**
     * @covers Mumsys_FileSystem::__construct
     */
    public function test__constructor()
    {
        $this->assertingInstanceOf( 'Mumsys_FileSystem', $this->_object );
    }


    /**
     * @covers Mumsys_FileSystem::scanDirInfo
     */
    public function testScanDirInfo()
    {
        @mkdir( $this->_testdirs['dir'], 0755 );
        @mkdir( $this->_testdirs['dirs'], 0755 );
        @touch( $this->_testdirs['dir'] . '/testfile' );
        @touch( $this->_testdirs['dirs'] . '/testfile' );
        // simple directory
        $actual1 = $this->_object->scanDirInfo( $this->_testdirs['dir'], true, false, array(), -1, 1001 );
        $expected1 = array(
            $this->_testsDir . '/tmp/unittest-mkdir/mkdirs' => array(
                'file' => $this->_testsDir . '/tmp/unittest-mkdir/mkdirs',
                'name' => 'mkdirs',
                'path' => $this->_testsDir . '/tmp/unittest-mkdir',
                'size' => 21,
                'type' => 'dir',
            ),
            $this->_testsDir . '/tmp/unittest-mkdir/testfile' => array(
                'file' => $this->_testsDir . '/tmp/unittest-mkdir/testfile',
                'name' => 'testfile',
                'path' => $this->_testsDir . '/tmp/unittest-mkdir',
                'size' => 0,
                'type' => 'file',
            )
        );

        // recursive directory
        $filers = array('/(unittest)/i');
        $actual2 = $this->_object->scanDirInfo( $this->_testdirs['dir'], true, true, $filers );
        $expected2 = array(
            $this->_testsDir . '/tmp/unittest-mkdir/mkdirs' => array(
                'file' => $this->_testsDir . '/tmp/unittest-mkdir/mkdirs',
                'name' => 'mkdirs',
                'path' => $this->_testsDir . '/tmp/unittest-mkdir',
                'size' => 21,
                'type' => 'dir',
            ),
            $this->_testsDir . '/tmp/unittest-mkdir/testfile' => array(
                'file' => $this->_testsDir . '/tmp/unittest-mkdir/testfile',
                'name' => 'testfile',
                'path' => $this->_testsDir . '/tmp/unittest-mkdir',
                'size' => 0,
                'type' => 'file',
            ),
            $this->_testsDir . '/tmp/unittest-mkdir/mkdirs/testfile' => array(
                'file' => $this->_testsDir . '/tmp/unittest-mkdir/mkdirs/testfile',
                'name' => 'testfile',
                'path' => $this->_testsDir . '/tmp/unittest-mkdir/mkdirs',
                'size' => 0,
                'type' => 'file',
            ),
        );
        unlink( $this->_testdirs['dir'] . '/testfile' );
        unlink( $this->_testdirs['dirs'] . '/testfile' );

        // unreadable path
        $actual3 = $this->_object->scanDirInfo( '/root', true, true );

        $p = $this->_testsDir . '/tmp/unittest-mkdir/mkdirs';
        $this->assertingEquals( $expected1[$p]['file'], $actual1[$p]['file'] );
        $this->assertingEquals( $expected1[$p]['name'], $actual1[$p]['name'] );
        $this->assertingEquals( $expected1[$p]['path'], $actual1[$p]['path'] );
        $this->assertingTrue( ( $expected1[$p]['size'] <= $actual1[$p]['size'] ) );
        $this->assertingEquals( $expected1[$p]['type'], $actual1[$p]['type'] );

        $p = $this->_testsDir . '/tmp/unittest-mkdir/testfile';
        $this->assertingEquals( $expected1[$p]['file'], $actual1[$p]['file'] );
        $this->assertingEquals( $expected1[$p]['name'], $actual1[$p]['name'] );
        $this->assertingEquals( $expected1[$p]['path'], $actual1[$p]['path'] );
        $this->assertingTrue( ( $expected1[$p]['size'] >= $actual1[$p]['size'] ) );
        $this->assertingEquals( $expected1[$p]['type'], $actual1[$p]['type'] );

        $p = $this->_testsDir . '/tmp/unittest-mkdir/mkdirs';
        $this->assertingEquals( $expected2[$p]['file'], $actual2[$p]['file'] );
        $this->assertingEquals( $expected2[$p]['name'], $actual2[$p]['name'] );
        $this->assertingEquals( $expected2[$p]['path'], $actual2[$p]['path'] );
        $this->assertingTrue( ( $expected2[$p]['size'] <= $actual2[$p]['size'] ) );
        $this->assertingEquals( $expected2[$p]['type'], $actual2[$p]['type'] );

        $p = $this->_testsDir . '/tmp/unittest-mkdir/testfile';
        $this->assertingEquals( $expected2[$p]['file'], $actual2[$p]['file'] );
        $this->assertingEquals( $expected2[$p]['name'], $actual2[$p]['name'] );
        $this->assertingEquals( $expected2[$p]['path'], $actual2[$p]['path'] );
        $this->assertingTrue( ( $expected2[$p]['size'] <= $actual2[$p]['size'] ) );
        $this->assertingEquals( $expected2[$p]['type'], $actual2[$p]['type'] );

        $p = $this->_testsDir . '/tmp/unittest-mkdir/mkdirs/testfile';
        $this->assertingEquals( $expected2[$p]['file'], $actual2[$p]['file'] );
        $this->assertingEquals( $expected2[$p]['name'], $actual2[$p]['name'] );
        $this->assertingEquals( $expected2[$p]['path'], $actual2[$p]['path'] );
        $this->assertingTrue( ( $expected2[$p]['size'] <= $actual2[$p]['size'] ) );
        $this->assertingEquals( $expected2[$p]['type'], $actual2[$p]['type'] );

        // view: https://regex101.com/r/sAt2MT/5
        // delete: https://regex101.com/delete/kYuk1QZ2z5xxxr7O73pPrWAz
        //  '/^(?!.*?(16x16|250x250|50x50))(?:\w+|.*)/' sub path containing the values should be ignored

        $this->assertingFalse( $actual3 );
    }


    /**
     * @covers Mumsys_FileSystem::getFileDetails
     * @covers Mumsys_FileSystem::_getFileDetailsPrepare
     */
    public function testGetFileDetails()
    {
        $actual1 = $this->_object->getFileDetails( __FILE__ );
        $expected1 = array(
            'file' => __FILE__,
            'name' => basename( __FILE__ ),
            'size' => filesize( __FILE__ ),
            'type' => 'file',
            'path' => dirname( __FILE__ ),
        );

        $actual2 = $this->_object->getFileDetails( dirname( __FILE__ ), basename( __FILE__ ) );

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected1, $actual2 );

        $this->expectingException( 'Mumsys_FileSystem_Exception' );
        $this->expectingExceptionMessage( 'File "/i/don/t/exist" not found' );
        $actual2 = $this->_object->getFileDetails( '/i/don/t/exist' );
    }


    /**
     * @covers Mumsys_FileSystem::getFileDetailsExtended
     * @covers Mumsys_FileSystem::_getFileDetailsPrepare
     */
    public function testGetFileDetailsExtended()
    {
        // info for a file
        $curFile = __FILE__;
        $stat = @lstat( $curFile );
        $actual1 = $this->_object->getFileDetailsExtended( $curFile );
        $expected1 = array(
            'file' => $curFile,
            'name' => basename( $curFile ),
            'size' => filesize( $curFile ),
            'type' => 'file',
            'path' => dirname( $curFile ),
            'is_file' => true,
            'is_dir' => false,
            'is_link' => false,
            'is_readable' => true,
            'is_writable' => true,
            'permission' => $stat['mode'],
            'owner' => $stat['uid'],
            'group' => $stat['gid'],
            'mtime' => $stat['mtime'],
            'atime' => $stat['atime'],
            'ctime' => $stat['ctime'],
            'filetype' => trim( shell_exec( 'file -b -p "' . $curFile . '";' ) ),
            'is_executable' => true,
            'ext' => 'php',
            'mimetype' => 'text/x-php',
            'owner_name' => @reset( posix_getpwuid( $stat['uid'] ) ),
            'group_name' => @reset( posix_getgrgid( $stat['gid'] ) ),
        );
        // info for a directory
        $actual2 = $this->_object->getFileDetailsExtended( $this->_testsDir . '/tmp' );
        $stat = @lstat( $this->_testsDir . '/tmp' );
        $expected2 = array(
            'file' => $this->_testsDir . '/tmp',
            'name' => 'tmp',
            'size' => filesize( $this->_testsDir . '/tmp' ),
            'type' => 'dir',
            'path' => $this->_testsDir,
            'is_file' => false,
            'is_dir' => true,
            'is_link' => false,
            'is_readable' => true,
            'is_writable' => true,
            'permission' => $stat['mode'],
            'owner' => $stat['uid'],
            'group' => $stat['gid'],
            'mtime' => $stat['mtime'],
            'atime' => $stat['atime'],
            'ctime' => $stat['ctime'],
            'filetype' => trim( shell_exec( 'file -b -p "' . $this->_testsDir . '/tmp";' ) ),
            'is_executable' => true,
            'ext' => false,
            'owner_name' => @reset( posix_getpwuid( $stat['uid'] ) ),
            'group_name' => @reset( posix_getgrgid( $stat['gid'] ) ),
        );
        // info for a link
        touch( $this->_testdirs['file'] );
        symlink( $this->_testdirs['file'], $this->_testsDir . '/tmp/link' );
        $stat = @lstat( $this->_testsDir . '/tmp/link' );
        $actual3 = $this->_object->getFileDetailsExtended( $this->_testsDir . '/tmp/link' );
        $expected3 = array(
            'file' => $this->_testsDir . '/tmp/link',
            'name' => 'link',
            'size' => $stat['size'],
            'type' => 'link',
            'path' => $this->_testsDir . '/tmp',
            'is_file' => true,
            'is_dir' => false,
            'is_link' => true,
            'is_readable' => true,
            'is_writable' => true,
            'permission' => $stat['mode'],
            'owner' => $stat['uid'],
            'group' => $stat['gid'],
            'mtime' => $stat['mtime'],
            'atime' => $stat['atime'],
            'ctime' => $stat['ctime'],
            'filetype' => $this->_object->getFileType( $this->_testsDir . '/tmp/link' ),
            'is_executable' => true,
            'ext' => '',
            'mimetype' => 'inode/x-empty',
            'target' => $this->_testsDir . '/tmp/unittest',
            'owner_name' => @reset( posix_getpwuid( $stat['uid'] ) ),
            'group_name' => @reset( posix_getgrgid( $stat['gid'] ) ),
        );
        @unlink( $this->_testsDir . '/tmp/link' );

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );
        $this->assertingEquals( $expected3, $actual3 );
    }


    /**
     * @covers Mumsys_FileSystem::getFileType
     */
    public function testGetFileType()
    {
        $actual = $this->_object->getFileType( '/bin/sh' );

        // OS related output
        $expecteds = array(
            "cannot open `/usr/bin/sh' (No such file or directory)\n",
            "ERROR: cannot open `/usr/bin/sh' (No such file or directory)\n",
            "finfo::file(/usr/bin/sh): failed to open stream: No such file or directory",
            "ELF 32-bit LSB shared object, Intel 80386, version 1 (SYSV)",
            'ELF 64-bit LSB executable, x86-64, version 1 (SYSV)',
            'ELF 64-bit LSB shared object, x86-64, version 1 (SYSV)',
        );
        $actual2 = $this->_object->getFileType( '/bin/ls' );

        $this->assertingTrue( in_array( $actual, $expecteds ), $actual );
        $this->assertingTrue( in_array( $actual2, $expecteds ), $actual2 );
    }


    /**
     * @covers Mumsys_FileSystem::copy
     */
    public function testCopy()
    {
        $actual1 = $this->_object->copy( $this->_testdirs['file'], $this->_testdirs['file2'] );
        $expected1 = $this->_testdirs['file2'];
        // keep copy
        $actual2 = $this->_object->copy( $this->_testdirs['file'], $this->_testdirs['file2'], true, 1 );
        $expected2 = $this->_testdirs['file2'] . '.2';
        @unlink( $expected2 );
        // target is a directory
        $actual3 = $this->_object->copy( $this->_testdirs['file'], $this->_testsDir . '/tmp', true );
        $expected3 = $this->_testdirs['file'] . '.1';
        @unlink( $expected3 );

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );
        $this->assertingEquals( $expected3, $actual3 );

        // source is a dir exception
        $msg = 'Source file: A directory was found. only file copying is implemented';
        $this->expectingExceptionMessage( $msg );
        $this->expectingException( 'Mumsys_FileSystem_Exception' );
        $this->_object->copy( $this->_testsDir . '/tmp/', '/home/' );
    }


    /**
     * @covers Mumsys_FileSystem::copy
     */
    public function testCopyException()
    {
        $msg = 'Copy error for: "' . $this->_testsDir . '/tmp/unittest" copy (to: /root/unittest) fails';
        $this->expectingExceptionMessage( $msg );
        $this->expectingException( 'Mumsys_FileSystem_Exception' );
        $this->_object->copy( $this->_testdirs['file'], '/root' );
    }


    /**
     * @covers Mumsys_FileSystem::rename
     */
    public function testRename()
    {
        // default copy
        $source = $this->_testdirs['dir'] . '/testfile';
        $expected1 = $this->_testdirs['dir'] . '/filetest';
        @mkdir( $this->_testdirs['dir'], 0755 );
        @touch( $source );
        $actual1 = $this->_object->rename( $source, $expected1 );
        // target exists
        @touch( $source );
        $actual2 = $this->_object->rename( $source, $expected1, true );
        $expected2 = $this->_testdirs['dir'] . '/filetest.1';
        //stream context
        @touch( $source );
        $streamCtx = stream_context_create();
        $actual3 = $this->_object->rename( $source, $expected1, false, $streamCtx );
        $expected3 = $this->_testdirs['dir'] . '/filetest';

        @unlink( $expected1 );
        @unlink( $expected2 );
        @rmdir( $this->_testdirs['dir'] );

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );
        $this->assertingEquals( $expected3, $actual3 );

        // source emty exception
        $msg = 'Rename failt for reason: Source "" is no directory and no file';
        $this->expectingExceptionMessage( $msg );
        $this->expectingException( 'Mumsys_FileSystem_Exception' );
        $this->_object->rename( '', $this->_testsDir . '/tmp/something' );
    }


    /**
     * @covers Mumsys_FileSystem::rename
     */
    public function testRenameException()
    {
        // rename permission error
        $msg[] = 'Rename failt for reason: Copy error for: "' . $this->_testsDir . '/tmp/unittest" '
            . 'copy(/root//unittest): failed to open stream: Permission denied';
        $msg[] = 'Rename failt for reason: rename(): Permission denied';
        $this->expectingException( 'Mumsys_FileSystem_Exception' );
        $this->_object->rename( $this->_testdirs['file'], '/root/' );
    }


    /**
     * @covers Mumsys_FileSystem::link
     */
    public function testLink()
    {
        $actual1 = $this->_object->link( $this->_testsDir . '/tmp', $this->_testdirs['dir'], 'soft', 'abs', false );
        $expected1 = $this->_testdirs['dir'];
        // is_link OK
        $actual2 = $this->_object->link( $this->_testsDir . '/tmp', $this->_testdirs['dir'], 'soft', 'abs', false );
        @unlink( $this->_testdirs['dir'] );

        // hard link (owned by myself ok, otherwise possible error/exceptions with write perms)
        touch( $this->_testdirs['dir'] );
        $actual3 = $this->_object->link(
            $this->_testdirs['dir'], $this->_testsDir . '/tmp/lnkname', 'hard', 'abs', false
        );
        $expected3 = $this->_testsDir . '/tmp/lnkname';
        @unlink( $this->_testsDir . '/tmp/lnkname' );
        @unlink( $this->_testdirs['dir'] );

        // relative links
        $actual4 = $this->_object->link( $this->_testsDir . '/tmp', $this->_testdirs['dir'], 'soft', 'rel', false );
        $expected4 = $this->_testdirs['dir'];

        // keep copy
        $actual5 = $this->_object->link( $this->_testsDir . '/tmp', $this->_testdirs['dir'], 'soft', 'abs', true );
        $expected5 = $this->_testdirs['dir'] . '.lnk';

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected1, $actual2 );
        $this->assertingEquals( $expected3, $actual3 );
        $this->assertingEquals( $expected4, $actual4 );
        $this->assertingEquals( $expected5, $actual5 );

        // test realpath exception + last, catched exception
        unlink( $this->_testdirs['dir'] );
        $msg = 'Linking failt for source: "' . $this->_testsDir . '/tmp"; target: "'
            . $this->_testsDir . '/tmp/unittest-mkdir/mkdirs". '
            . 'Real path not found for "' . $this->_testsDir . '/tmp/unittest-mkdir"';
        $this->expectingExceptionMessage( $msg );
        $this->expectingException( 'Mumsys_FileSystem_Exception' );
        $this->_object->link( $this->_testsDir . '/tmp', $this->_testdirs['dirs'], 'soft', 'rel', false );
    }


    /**
     * @covers Mumsys_FileSystem::link
     */
    public function testLinkException()
    {
        // invalid link type
        $msg = 'Linking failt for source: "' . $this->_testsDir . '/tmp"; target: "'
            . $this->_testsDir . '/tmp/unittest-mkdir". '
            . 'Invalid link type "invalidType"  (Use soft|hard)';
        $this->expectingExceptionMessage( $msg );
        $this->expectingException( 'Mumsys_FileSystem_Exception' );
        $this->_object->link( $this->_testsDir . '/tmp', $this->_testdirs['dir'], 'invalidType', 'rel', false );
    }

    /**
     * @covers Mumsys_FileSystem::unlink
     * @covers Mumsys_FileSystem::rmFile
     */
    public function testRemoveFile()
    {
        $file = $this->_testsDir . '/tmp/unlink.test';
        $link = $this->_testsDir . '/tmp/unlink.test.link';
        touch( $file );
        symlink( $file, $link );

        $this->assertingTrue( file_exists( $file ) );
        $this->assertingTrue( file_exists( $link ) );

        $this->assertingTrue( $this->_object->unlink( $link ) );
        $this->assertingTrue( $this->_object->rmfile( $file ) );

        $this->assertingTrue( $this->_object->unlink( $this->_testsDir ) );

        // test exception
        /** @todo file with different ownership */
//        $this->expectingException('Mumsys_FileSystem_Exception', '/(Can not delete file "'.$file.'")/i');
//        $this->_object->unlink($file);
    }


    /**
     * @covers Mumsys_FileSystem::mkdir
     */
    public function testMkdir()
    {
        $dir = $this->_testdirs['dir'];
        $this->_object->mkdir( $dir );

        $this->assertingTrue( file_exists( $dir ) );
        $this->assertingFalse( $this->_object->mkdir( $dir ) ); // exists error

        $exMesg = 'Can not create dir: "/xyz" mode: "755". Message: mkdir(): Permission denied';
        $this->expectingExceptionMessage( $exMesg );
        $this->expectingException( 'Mumsys_FileSystem_Exception' );
        $this->_object->mkdir( '/xyz' );
    }


    /**
     * @covers Mumsys_FileSystem::mkdirs
     * @covers Mumsys_FileSystem::mkdir
     *
     * @todo how to test for 100% code coverage?
     */
    public function testMkdirs()
    {
        $dir = $this->_testdirs['dirs'];
        $this->_object->mkdirs( $dir );

        $this->assertingTrue( file_exists( $dir ) ); // created
        $this->assertingTrue( $this->_object->mkdirs( $dir ) ); // exists
    }


    /**
     * Test mkdir fails and rm already created.
     *
     * @covers Mumsys_FileSystem::mkdirs
     * @covers Mumsys_FileSystem::mkdir
     *
     */
    public function testMkdirsException()
    {
        $dir = $this->_testdirs['dirs'] . '/x/';
        $this->_object->mkdirs( $this->_testdirs['dirs'] . '/x/../../home/user', 0700 );
        $this->assertingFalse( file_exists( $dir ) );

        $this->assertingTrue( $this->_object->mkdirs( $dir ) ); // created
        $this->assertingTrue( file_exists( $dir ) );

        @rmdir( $dir );
    }

    /**
     * @covers Mumsys_FileSystem::rmdir
     * @covers Mumsys_FileSystem::mkdir
     */
    public function testRmDir()
    {
        $dir = $this->_testsDir . '/tmp/rmdir';
        $this->_object->mkdir( $dir, 0755 );

        $this->assertingTrue( is_dir( $dir ) );
        $this->assertingTrue( $this->_object->rmdir( $dir . '/test/' ) );

        $this->_object->rmdir( $dir );

        $this->assertingFalse( is_dir( $dir ) );

        // test exception
        $regex = '/(Can not delete directory ")/i';
        $this->expectingException( 'Mumsys_FileSystem_Exception' );
        $this->_object->rmdir( '/root/' );
    }


    /**
     * @covers Mumsys_FileSystem::rmdirs
     * @covers Mumsys_FileSystem::mkdirs
     * @covers Mumsys_FileSystem::unlink
     * @covers Mumsys_FileSystem::mkdir
     */
    public function testRmDirs()
    {
        $baseDir = $this->_testdirs['dirs'];
        $dir = $this->_testdirs['dirs'] . '/rmdirs/x/y/';

        $this->assertingTrue( $this->_object->rmdirs( $dir ) );

        $actual1 = $this->_object->mkdirs( $dir, 0755 );
        touch( $dir . '/test.file' );

        $this->assertingTrue( $actual1 );
        $this->assertingTrue( is_dir( $dir ) );

        $actual2 = $this->_object->rmdirs( $baseDir );
        $this->assertingTrue( $actual2 );
        $this->assertingFalse( is_dir( $dir ) );

        // test exception
        $this->expectingException( 'Exception' );
        $this->_object->rmdirs( '/root/' );
    }


    /**
     * @covers Mumsys_FileSystem::getRelativeDir
     * @todo   Implement testGetRelativeDir().
     */
    public function testGetRelativeDir()
    {
        $actual1 = $this->_object->getRelativeDir( '/tmp/here', '/home/there' );
        $expected1 = '../../home/there/';

        $actual2 = $this->_object->getRelativeDir( '/home/user/mydir', '/share/data/thatdir' );
        $expected2 = '../../../share/data/thatdir/';

        $actual3 = $this->_object->getRelativeDir( './myfile', './data/thatdir' );
        $expected3 = '../data/thatdir/';

        $actual4 = $this->_object->getRelativeDir(
            $this->_testsDir . '/tmp/',
            $this->_testsDir . '/tmp/unittest-mkdir/mkdirs/'
        );
        $expected4 = './unittest-mkdir/mkdirs/';

        $this->assertingEquals( $expected1, $actual1 );
        $this->assertingEquals( $expected2, $actual2 );
        $this->assertingEquals( $expected3, $actual3 );
        $this->assertingEquals( $expected4, $actual4 );
    }


    /**
     * @covers Mumsys_FileSystem::coolfilesize
     */
    public function testCoolfilesize()
    {
        $actual = array();
        $actual[] = $this->_object->coolfilesize( 1000, 2 );
        $actual[] = $this->_object->coolfilesize( 10000, 2 );
        $actual[] = $this->_object->coolfilesize( 10000000, 2 );
        $actual[] = $this->_object->coolfilesize( 10000000000, 2 );
        $actual[] = $this->_object->coolfilesize( 10000000000000, 2 );
        $actual[] = $this->_object->coolfilesize( 1000000000000000, 2 );
        $expected = array(
            '1000 Bytes',
            '10 KB',
            '10 MB',
            '10 GB',
            '10 TB',
            '1000 TB',
        );
        $this->assertingEquals( $expected, $actual );
    }


    // --- test abstract and versions


    public function testgetVersions()
    {
        $possible = $this->_object->getVersions();

        foreach ( $this->_versions as $must => $value ) {
            $this->assertingTrue( isset( $possible[$must] ) );
            $this->assertingEquals( $possible[$must], $value );
        }
    }

}
