<?php

namespace RamosISW\Jwt\Claims;

use ADmad\JwtAuth\Auth\JwtAuthenticate;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;

/**
 * @property \Cake\Controller\Component\AuthComponent $Auth
 * @property \ADmad\JwtAuth\Auth\JwtAuthenticate $Jwt
 *
 */
class ClaimsComponent extends Component
{

    /**
     * Components
     * @var array
     */
    public $components = [
        'Auth'
    ];

    /**
     * ClaimsComponent constructor.
     * @param \Cake\Controller\ComponentRegistry $registry The Component registry used on this request.
     * @param array $config Array of config to use
     */
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $defaultConfig = [
            'claims_key' => 'data',
            'cookie' => 'jwt',
            'key' => null,
            'allowedAlgs' => ['HS256'],
            'claims' => []
        ];
        $this->setConfig($defaultConfig);

        $config['claims'] = $config[isset($config['claims_key']) ? $config['claims_key'] : $defaultConfig['claims_key']];
        parent::__construct($registry, $config);
    }

    /**
     * @param array $config Array of config to use
     * @throws \Exception
     * @return void
     */
    public function initialize(array $config)
    {
        $authConfig = $this->Auth->getConfig('authenticate');

        if (!(isset($authConfig['ADmad/JwtAuth.Jwt']) && is_array($authConfig['ADmad/JwtAuth.Jwt']))) {
            throw new \Exception('ADmad/JwtAuth.Jwt not set in authenticate config');
        }

        $this->Jwt = $this->Auth->getAuthenticate('ADmad/JwtAuth.Jwt');

        if ($this->Jwt instanceof JwtAuthenticate) {
            $payload = $this->Jwt->getPayload($this->_registry->getController()->getRequest());

            $claims_key = $this->_config['claims_key'];
            $data = $this->_config[$claims_key];
            if (isset($data) && is_array($data)) {
                foreach ($data as $key) {
                    if (isset($payload->$claims_key) && isset($payload->$claims_key->$key)) {
                        $this->$key = $payload->$claims_key->$key;
                    }
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public function getClaims()
    {
        return $this->_config['claims'];
    }
}
