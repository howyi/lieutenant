<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use LaravelDoctrine\Extensions\Timestamps\Timestamps;

/**
 * Authenticate
 *
 * @ORM\Table(name="authenticates")
 * @ORM\Entity
 */
class Authenticate implements \JsonSerializable
{
    use Timestamps;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $authenticateId;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="service_name")
     */
    private $serviceName;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $serviceId;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="authenticates")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="AuthenticateService", inversedBy="authenticate")
     * @ORM\JoinColumn(name="service_name", referencedColumnName="service_name")
     */
    private $authenticateService;

    /**
     * Authenticate constructor.
     *
     * @param User   $user
     * @param string $serviceName
     * @param string $serviceId
     */
    public function __construct(
        User $user,
        string $serviceName,
        string $serviceId
    ) {
        $this->user = $user;
        $this->userId = $user->getUserId();
        $this->serviceName = $serviceName;
        $this->serviceId = $serviceId;
    }

    /**
     * Set userId.
     *
     * @param string $userId
     *
     * @return Authenticate
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId.
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set serviceName.
     *
     * @param string $serviceName
     *
     * @return Authenticate
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;

        return $this;
    }

    /**
     * Get serviceName.
     *
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * Set serviceId.
     *
     * @param string $serviceId
     *
     * @return Authenticate
     */
    public function setServiceId($serviceId)
    {
        $this->serviceId = $serviceId;

        return $this;
    }

    /**
     * Get serviceId.
     *
     * @return string
     */
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * Get user.
     *
     * @return User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'service_name' => $this->getServiceName(),
            'service_id' => $this->getServiceId(),
        ];
    }
}
