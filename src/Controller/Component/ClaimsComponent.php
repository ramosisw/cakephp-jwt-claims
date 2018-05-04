<?php

namespace RamosISW\Jwt\Controller\Component;

use ADmad\JwtAuth\Auth\JwtAuthenticate;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Exception\MissingPluginException;

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
     * @var string plugin name used by this component
     */
    private $jwtPlugin = 'ADmad/JwtAuth.Jwt';

    /**
     * ClaimsComponent constructor.
     * @param \Cake\Controller\ComponentRegistry $registry The Component registry used on this request.
     * @param array $config Array of config to use
     */
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $defaultConfig = [
            'claims_key' => 'claims'
        ];
        $this->setConfig($defaultConfig);

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

        if (!(isset($authConfig[$this->jwtPlugin]) && is_array($authConfig[$this->jwtPlugin]))) {
            throw new MissingPluginException([$this->jwtPlugin]);
        }

        $this->Jwt = $this->Auth->getAuthenticate($this->jwtPlugin);

        if ($this->Jwt instanceof JwtAuthenticate) {
            $payload = $this->Jwt->getPayload($this->_registry->getController()->getRequest());

            $claims_key = $this->getClaimsKey();
            $data = $this->getClaims();
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
     * @return mixed claims key to find in payload object
     */
    public function getClaimsKey()
    {
        return $this->_config['claims_key'];
    }

    /**
     * @return mixed claims
     */
    public function getClaims()
    {
        $claims_key = $this->_config['claims_key'];

        return $this->_config[$claims_key];
    }
}
