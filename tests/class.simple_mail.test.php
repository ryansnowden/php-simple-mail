<?php

date_default_timezone_set('Europe/Dublin');

require_once(__DIR__ . '/../class.simple_mail.php');

class testSimpleMail extends PHPUnit_Framework_TestCase
{
    /**
     * @var SimpleMail
     */
    protected $mailer;

    protected $directory;

    public function setUp()
    {
        $this->mailer    = new SimpleMail();
        $this->directory = realpath('./');
    }

    public function testSetToWithExpectedValues()
    {
        $this->mailer->setTo('test@gmail.com', 'Tester');
        $this->assertContains('Tester <test@gmail.com>', $this->mailer->getTo());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetToThrowsInvalidArgumentExceptionWithInvalidEmail()
    {
        $this->mailer->setTo(123, 'Tester');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetToThrowsInvalidArgumentExceptionWithInvalidName()
    {
        $this->mailer->setTo('test@gmail.com', 123);
    }

    public function testSetToAddsHeader()
    {
        $this->mailer->setTo('test@gmail.com', 'Tester');
        $header = $this->mailer->formatHeader('test@gmail.com', 'Tester');

        $this->assertContains($header, $this->mailer->getTo());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetSubjectThrowsInvalidArgumentExceptionWithInvalidSubject()
    {
        $this->mailer->setSubject(12345);
    }

    public function testSetSubjectReturnsCorrectValue()
    {
        $this->mailer->setSubject('Testing Simple Mail');

        $this->assertSame($this->mailer->getSubject(), 'Testing Simple Mail');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetMessageThrowsInvalidArgumentExceptionWithInvalidMessage()
    {
        $this->mailer->setMessage(123);
    }

    public function testSetMessageReturnsCorrectValue()
    {
        $this->mailer->setMessage('Testing Simple Mail');

        $this->assertSame($this->mailer->getMessage(), 'Testing Simple Mail');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetFromThrowsInvalidArgumentWithInvalidEmail()
    {
        $this->mailer->setFrom(123, 'Tester');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetMessageThrowsInvalidArgumentWithInvalidName()
    {
        $this->mailer->setFrom('test@gmail.com', 123);
    }

    public function testSetMessageIsAddedToHeaders()
    {
        $this->mailer->setFrom('test@gmail.com', 'Tester', true);
        $header = sprintf('%s: %s', 'From', $this->mailer->formatHeader('test@gmail.com', 'Tester'));

        $this->assertContains($header, $this->mailer->getHeaders());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetWrapThrowsInvalidArgumentExceptionWithNonInt()
    {
        $this->mailer->setWrap('non int');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetWrapThrowsInvalidArgumentExceptionWithZero()
    {
        $this->mailer->setWrap(0);
    }

    public function testSetWrapAssignsCorrectValue()
    {
        $this->mailer->setWrap(50);

        $this->assertSame(50, $this->mailer->getWrap());
    }

    public function testGetWrapDefaultsTo78()
    {
        $this->assertSame(78, $this->mailer->getWrap());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddMailHeaderThrowsInvalidArgumentExceptionWithInvalidHeader()
    {
        $this->mailer->addMailHeader(123);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddMailHeaderThrowsInvalidArgumentExceptionWithInvalidEmail()
    {
        $this->mailer->addMailHeader('Testing', 213);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddMailHeaderThrowsInvalidArgumentExceptionWithInvalidName()
    {
        $this->mailer->addMailHeader('Testing', 'testing@gmail.com', 123);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetParametersThrowsInvalidArgumentExceptionWithInvalidParams()
    {
        $this->mailer->setParameters(123);
    }

    public function testSetParametersReturnsCorrectString()
    {
        $this->mailer->setParameters("-fuse@gmail.com");
        $params = $this->mailer->getParameters();

        $this->assertSame("-fuse@gmail.com", $params);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddGenericHeaderThrowsInvalidArgumentExceptionWithInvalidHeader()
    {
        $this->mailer->addGenericHeader(false, 'Value');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddGenericHeaderThrowsInvalidArgumentExceptionWithInvalidValue()
    {
        $this->mailer->addGenericHeader('Version', false);
    }

    public function testAddGenericHeaderReturnsCorrectHeader()
    {
        $this->mailer->addGenericHeader('Version', 'PHP5');
        $this->assertContains("Version: PHP5", $this->mailer->getHeaders());
    }

    public function testFormatHeaderWithoutNameReturnsOnlyTheEmail()
    {
        $email  = 'test@domain.tld';
        $header = $this->mailer->formatHeader($email);

        $this->assertSame($email, $header);
    }

    public function testDebug()
    {
        $this->assertSame($this->mailer->debug(), '<pre>'.print_r($this->mailer, 1).'</pre>');
    }

    public function testToString()
    {
        $stringObject = print_r($this->mailer, 1);

        $this->assertSame((string) $this->mailer, $stringObject);
    }

    public function testHasAttachmentsReturnsTrueWithAttachmentPassed()
    {
        $this->mailer->addAttachment($this->directory.'/example/pbXBsZSwgY2hh.jpg', 'lolcat_finally_arrived.jpg');

        $this->assertTrue($this->mailer->hasAttachments());
    }

    public function testHasAttachmentsReturnsFalseWithNoAttachmentPassed()
    {
        $this->assertFalse($this->mailer->hasAttachments());
    }

    public function testAssembleAttachmentReturnsString()
    {
        $this->mailer->addAttachment($this->directory.'/example/pbXBsZSwgY2hh.jpg', 'lolcat_finally_arrived.jpg');

        $this->assertTrue(is_string($this->mailer->assembleAttachmentHeaders()));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testSendThrowsRuntimeExceptionWhenNoToAddressIsSet()
    {
        $this->mailer->send();
    }

    public function testSendReturnsBoolean()
    {
        $this->mailer->setTo('test@gmail.com', "Recipient")
                     ->setFrom('tester@gmail.com', 'Tester')
                     ->setSubject('Hello From PHPUnit')
                     ->setMessage('Hello message.');

        $bool = $this->mailer->send();
        $this->assertTrue(is_bool($bool));
    }

    public function testSendAttachmentReturnsBoolean()
    {
        $this->mailer->setTo('test@gmail.com', "Recipient")
                     ->setFrom('tester@gmail.com', 'Tester')
                     ->setSubject('Hello From PHPUnit')
                     ->setMessage('Hello message.')
                     ->addAttachment($this->directory.'/example/pbXBsZSwgY2hh.jpg', 'lolcat_finally_arrived.jpg');

        $bool = $this->mailer->send();
        $this->assertTrue(is_bool($bool));
    }

    public function testFilterNameRemovesCarriageReturns()
    {
        $string = "\rHello World";
        $name = $this->mailer->filterName("\rHello World");

        $this->assertNotSame($string, $name);
    }

    public function testFilterNameRemovesNewLines()
    {
        $string = "\nHello World";
        $name = $this->mailer->filterName($string);

        $this->assertNotSame($string, $name);
    }

    public function testFilterNameRemovesTabbedChars()
    {
        $string = "\tHello World\t";
        $name = $this->mailer->filterName($string);

        $this->assertNotSame($string, $name);
    }

    public function testFilterNameReplacesDoubleQuotesWithSingleQuoteEntities()
    {
        $expected = "&#34;Hello World&#34;";
        $name     = $this->mailer->filterName('"Hello World"');

        $this->assertEquals($expected, $name);
    }

    public function testFilterNameRemovesAngleBrackets()
    {
        $expected = 'Hello World';
        $name     = $this->mailer->filterName('<> Hello World');

        $this->assertEquals($expected, $name);
    }

    public function testFilterOtherRemovesCarriageReturns()
    {
        $expected = 'Hello World';
        $actual   = $this->mailer->filterOther("\rHello World");
        $this->assertSame($expected, $actual);
    }

    public function testFilterOtherRemovesNewLines()
    {
        $expected = 'Hello World';
        $actual   = $this->mailer->filterOther("\nHello World");
        $this->assertSame($expected, $actual);
    }

    public function testFilterOtherRemovesTabbedChars()
    {
        $expected = 'Hello World';
        $actual   = $this->mailer->filterOther("\tHello World");
        $this->assertSame($expected, $actual);
    }

    public function testFilterEmailRemovesCarriageReturns()
    {
        $string = "test@gmail.com\r";
        $name = $this->mailer->filterName($string);

        $this->assertNotSame($string, $name);
    }

    public function testFilterEmailRemovesNewLines()
    {
        $string = "test@gmail.com\n";
        $name = $this->mailer->filterName($string);

        $this->assertNotSame($string, $name);
    }

    public function testFilterEmailRemovesTabbedChars()
    {
        $string = "\tHello World\t";
        $name = $this->mailer->filterName($string);

        $this->assertNotSame($string, $name);
    }

    public function testFilterEmailReplacesDoubleQuotesWithSingleQuoteEntities()
    {
        $expected = "&#34;Hello World&#34;";
        $name     = $this->mailer->filterName('"Hello World"');

        $this->assertEquals($expected, $name);
    }

    public function testFilterEmailRemovesAngleBrackets()
    {
        $expected = 'Hello World';
        $name     = $this->mailer->filterName('<> Hello World');

        $this->assertEquals($expected, $name);
    }

    public function tearDown()
    {
        unset($this->mailer);
    }
}