<?php

namespace RamosISW\Jwt\Claims;

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
     * @param ComponentRegistry $registry
     * @param array $config
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
     * @param array $config
     * @throws \Exception
     */
    public function initialize(array $config)
    {

        $authConfig = $this->Auth->getConfig('authenticate');
        if (!(isset($authConfig['ADmad/JwtAuth.Jwt']) && is_array($authConfig['ADmad/JwtAuth.Jwt'])))
            throw new \Exception('ADmad/JwtAuth.Jwt not set in authenticate config');

        $this->Jwt = $this->Auth->getAuthenticate('ADmad/JwtAuth.Jwt');

        $payload = $this->Jwt->getPayload($this->_registry->getController()->getRequest());

        $claims_key = $this->_config['claims_key'];
        $data = $this->_config[$claims_key];
        if (isset($data) && is_array($data)) {
            foreach ($data as $key) {
                if (isset($payload->$claims_key) && isset($payload->$claims_key->$key))
                    $this->$key = $payload->$claims_key->$key;
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