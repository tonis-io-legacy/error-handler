<?php

namespace Tonis\ErrorHandler;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

/**
 * @covers \Tonis\ErrorHandler\ErrorHandler
 */
class ErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testInvokeProxiesToLogger()
    {
        $ex       = new \RuntimeException('foo');
        $test     = new TestHandler();
        $request  = ServerRequestFactory::fromGlobals();
        $response = new Response();
        $logger   = new Logger('default', [$test]);
        $error    = new ErrorHandler($logger);

        $response = $error(
            $ex,
            $request,
            $response,
            function ($request, $response) {
                $response->getBody()->write('complete');
                return $response;
            }
        );

        $this->assertSame('complete', $response->getBody()->__toString());
        $this->assertCount(1, $test->getRecords());
    }
}
