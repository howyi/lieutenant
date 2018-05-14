<?php

namespace App\Http\Controllers;

use App\Models\User\Authenticate;
use App\Models\User\User;
use App\Modifiers\AuthenticateServiceManager;
use App\Modifiers\UserModifier;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Contracts\User as LinkedAccount;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{
    /**
     * @var JWTAuth
     */
    protected $jwt;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var UserModifier
     */
    protected $userModifier;

    /**
     * @var AuthenticateServiceManager
     */
    protected $authenticateServiceManager;

    /**
     * AuthController constructor.
     * @param JWTAuth                    $jwt
     * @param EntityManager              $em
     * @param UserModifier               $userModifier
     * @param AuthenticateServiceManager $authenticateServiceManager
     */
    public function __construct(
        JWTAuth $jwt,
        EntityManager $em,
        UserModifier $userModifier,
        AuthenticateServiceManager $authenticateServiceManager
    ) {
        $this->jwt = $jwt;
        $this->em = $em;
        $this->userModifier = $userModifier;
        $this->authenticateServiceManager = $authenticateServiceManager;
    }

    /**
     * @param Request $request
     * @param string  $service
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect(Request $request, string $service)
    {
	    dump(\Auth::user());
        return $this
            ->authenticateServiceManager
            ->getDriver($request, $service)
            ->stateless()
            ->with(['aaa' => 'goooood'])
            ->redirect();
    }

    /**
     * @param Request $request
     * @param string  $service
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function callback(Request $request, string $service)
    {
        $linkedAccount = $this
            ->authenticateServiceManager
            ->getDriver($request, $service)
            ->stateless()
            ->user();

        dump($linkedAccount);

        $user = $this->findOrCreateUser($linkedAccount, $service);
        $token = $this->jwt->fromUser($user);

        return response()->json([
            'user' => $user,
            'token' => $token,
            'tokenExpiresIn' => auth()->factory()->getTTL() * 60,
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * @return JsonResponse
     */
    public function refresh()
    {
        return response()->json([
            'token' => auth()->refresh(),
            'tokenExpiresIn' => auth()->factory()->getTTL() * 60,
        ]);
    }

    /**
     * 接続済み外部アカウント情報からvoileユーザ情報を返す
     *
     * @param LinkedAccount $linkedAccount
     * @param string        $service
     * @return User
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function findOrCreateUser(LinkedAccount $linkedAccount, string $service): User
    {
        $authenticate = $this
            ->em
            ->getRepository(Authenticate::class)
            ->findOneBy([
                'serviceName' => $service,
                'serviceId'   => $linkedAccount->getId(),
            ]);

        if (is_null($authenticate)) {
            return $this->userModifier->create($linkedAccount, $service);
        } else {
            return $authenticate->getUser();
        }
    }
}
