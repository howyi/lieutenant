<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use LaravelDoctrine\Extensions\Timestamps\Timestamps;

/**
 * AuthenticateService
 *
 * @ORM\Table(name="authenticate_services")
 * @ORM\Entity
 */
class AuthenticateService
{
    use Timestamps;

    /**
     * @var string
     *
     * @ORM\Column
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $serviceName;

    /**
     * @var string
     *
     * @ORM\Column(type="authenticateServiceType")
     */
    private $serviceType;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $clientId;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $redirectUrl;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $clientSecret;

    /**
     * @return string
     */
    public function getServiceType()
    {
        return $this->serviceType;
    }

    /**
     * @return string[]
     */
    public function getKeys(): array
    {
        return [
            $this->clientId,
            $this->clientSecret,
            $this->redirectUrl
        ];
    }
}
