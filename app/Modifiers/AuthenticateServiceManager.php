<?php

namespace App\Modifiers;

use App\Enums\AuthenticateServiceType;
use App\Models\AuthenticateService;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\Request;
use Laravel\Socialite\Two\AbstractProvider;

class AuthenticateServiceManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return AuthenticateService[]
     */
    public function findAll(): array
    {
        return $this
            ->em
            ->getRepository(AuthenticateService::class)
            ->findAll();
    }

    /**
     * @param Request $request
     * @param string  $serviceName
     * @return AbstractProvider
     */
    public function getDriver(
        Request $request,
        string $serviceName
    ): AbstractProvider {
        /** @var AuthenticateService $service */
        $service = $this
            ->em
            ->getRepository(AuthenticateService::class)
            ->findOneBy([
                'serviceName' => $serviceName,
            ]);

        if (is_null($service)) {
            throw new \RuntimeException('Not support service: ' . $serviceName);
        }

        $type = $service->getServiceType();

        $providerName = AuthenticateServiceType::PROVIDERS[$type];
        [$clientId, $clientSecret, $redirectUrl] = $service->getKeys();

        /** @var AbstractProvider $provider */
        $provider = new $providerName(
            $request,
            $clientId,
            $clientSecret,
            $redirectUrl
        );

        if (isset(AuthenticateServiceType::SCOPES[$type])) {
            $provider->scopes(AuthenticateServiceType::SCOPES[$type]);
        }

        return $provider;
    }
}
