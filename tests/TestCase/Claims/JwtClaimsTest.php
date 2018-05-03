<?php

namespace RamosISW\Jwt\Claims\Test\TestCase\Claims;

use Cake\Controller\Component\AuthComponent;
use Cake\Controller\ComponentRegistry;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\Utility\Security;
use Firebase\JWT\JWT;
use RamosISW\Jwt\Claims\ClaimsComponent;

/**
 * Test case for JwtClaims.
 */
class JwtClaimsTest extends TestCase
{
    /**
     * @var string to set on authorization
     */
    private $token;

    /**
     * @var \Cake\Controller\Controller base off request
     */
    private $controller;

    /**
     * @var \RamosISW\Jwt\Claims\ClaimsComponent component of test
     */
    private $Component;

    public $fixtures = [
        'plugin.RamosISW\Jwt.users',
        'plugin.RamosISW\Jwt.groups',
    ];

    /**
     * Setup tests
     */
    public function setUp()
    {
        parent::setUp();

        Security::setSalt('secret-key');

        $this->token = JWT::encode([
            'sub' => 1,
            'data' => [
                'user_id' => 1,
                'user_name' => 'jcramos',
                'email' => 'jcramos@example.com'
            ]
        ], Security::getSalt());
    }

    /**
     * @param $request ServerRequest add to Controller
     */
    private function setRequestController($request)
    {
        $response = new Response();
        $this->controller = $this->getMockBuilder('Cake\Controller\Controller')
            ->setConstructorArgs([$request, $response])
            ->setMethods(null)
            ->getMock();
    }

    /**
     * @param $registry
     * @param array $config Configuration to Auth Component
     */
    private function registryAuth($registry, $config = [])
    {
        $_config = [
            'parameter' => 'token',
            'userModel' => 'Users',
            'fields' => [
                'username' => 'id'
            ],
        ];
        foreach ($config as $key => $value) {
            if (isset($_config[$key])) {
                $_config[$key] = $value;
            }
        }
        $registry->Auth = new AuthComponent($registry, ['authenticate' => [
            'ADmad/JwtAuth.Jwt' => $_config
        ]]);
    }

    /**
     * Test Claims
     */
    public function testClaims()
    {
        $request = new ServerRequest([
            'url' => 'posts/index?token=' . $this->token
        ]);

        $this->setRequestController($request);

        $registry = new ComponentRegistry($this->controller);

        $this->registryAuth($registry);
        $this->Component = new ClaimsComponent($registry, [
            'claims_key' => 'data',
            'data' => [
                'user_id', 'user_name', 'email'
            ]
        ]);

        $claims = [
            'user_id', 'user_name', 'email'
        ];

        $this->assertEquals($claims, $this->Component->getClaims());
    }

    /**
     * Test read Claims from JWT Query Token
     */
    public function testAuthParameter()
    {
        $request = new ServerRequest([
            'url' => 'posts/index?token=' . $this->token
        ]);

        $this->setRequestController($request);

        $registry = new ComponentRegistry($this->controller);

        $this->registryAuth($registry);
        $this->Component = new ClaimsComponent($registry, [
            'claims_key' => 'data',
            'data' => [
                'user_id', 'user_name', 'email'
            ]
        ]);
        $user_name = "jcramos";
        $user_id = 1;
        $email = "jcramos@example.com";

        $this->assertEquals($user_id, $this->Component->user_id);
        $this->assertEquals($user_name, $this->Component->user_name);
        $this->assertEquals($email, $this->Component->email);
    }

    /**
     * testConfig.
     *
     * @return void
     */
    public function testAuthHeader()
    {
        $request = new ServerRequest([
            'url' => 'posts/index'
        ]);
        $request = $request->withEnv('HTTP_AUTHORIZATION', 'Bearer ' . $this->token);

        $this->setRequestController($request);

        $registry = new ComponentRegistry($this->controller);

        $this->registryAuth($registry);
        $this->Component = new ClaimsComponent($registry, [
            'claims_key' => 'data',
            'data' => [
                'user_id', 'user_name', 'email'
            ]
        ]);
        $user_name = "jcramos";
        $user_id = 1;
        $email = "jcramos@example.com";

        $this->assertEquals($user_id, $this->Component->user_id);
        $this->assertEquals($user_name, $this->Component->user_name);
        $this->assertEquals($email, $this->Component->email);
    }
}
