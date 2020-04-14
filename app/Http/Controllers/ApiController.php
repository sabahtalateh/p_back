<?php

namespace App\Http\Controllers;

use App\Dish;
use App\Service\AuthService;
use App\Service\OrderService;
use App\Service\TokenService;
use Symfony\Component\HttpFoundation\Request;

class ApiController
{
    public function token()
    {
        /** @var TokenService $tokenService */
        $tokenService = app()->get(TokenService::class);
        $token = $tokenService->create();
        return [
            'token' => $token->token,
        ];
    }

    public function login(Request $request)
    {
        $token = $request->headers->get('X-TOKEN');

        $email = $request->get('email');
        if (null === $email) {
            return response(['errors' => ['No Email provided']], 400);
        }

        $password = $request->get('password');
        if (null === $password) {
            return response(['errors' => ['No password provided']], 400);
        }

        /** @var AuthService $authService */
        $authService = app()->get(AuthService::class);
        $authorized = $authService->authorize($token, $email, $password);

        if ($authorized) {
            return ['success' => true];
        } else {
            return response(['success' => false], 403);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->headers->get('X-TOKEN');

        /** @var AuthService $authService */
        $authService = app()->get(AuthService::class);
        return ['success' => $authService->logout($token)];
    }

    public function user(Request $request)
    {
        $token = $request->headers->get('X-TOKEN');

        /** @var AuthService $authService */
        $authService = app()->get(AuthService::class);
        $result = $authService->userInfo($token);
        if (isset($result['errors']) && count($result['errors']) > 0) {
            return response(['errors' => $result['errors']], 400);
        } else {
            return response($result);
        }
    }

    public function dishes()
    {
        return Dish::all();
    }

    public function toCart(Request $request)
    {
        $token = $request->headers->get('X-TOKEN');

        $dishId = $request->get('dishId');
        if (null === $dishId) {
            return response(['errors' => ['No dish ID provided']], 400);
        }

        /** @var OrderService $orderService */
        $orderService = app()->get(OrderService::class);
        $result = $orderService->addDishToOrder($dishId, $token);

        if (isset($result['errors']) && count($result['errors']) > 0) {
            return response(['errors' => $result['errors']], 400);
        } else {
            return response(['dishesInCart' => $result['amount']]);
        }
    }

    public function dishesInCart(Request $request)
    {
        /** @var OrderService $orderService */
        $orderService = app()->get(OrderService::class);
        $result = $orderService->countDishesInOrder($request->headers->get('X-TOKEN'));

        if (isset($result['errors']) && count($result['errors']) > 0) {
            return response(['errors' => $result['errors']], 400);
        } else {
            return response(['dishesInCart' => $result['amount']]);
        }
    }

    public function cart(Request $request)
    {
        /** @var OrderService $orderService */
        $orderService = app()->get(OrderService::class);
        $result = $orderService->getActiveOrder($request->headers->get('X-TOKEN'));

        if (isset($result['errors']) && count($result['errors']) > 0) {
            return response(['errors' => $result['errors']], 400);
        } else {
            return response($result);
        }
    }

    public function removeFromCart(Request $request)
    {
        $recordId = $request->get('recordId');
        if (null === $recordId) {
            return response(['errors' => ['No Record ID provided']], 400);
        }

        /** @var OrderService $orderService */
        $orderService = app()->get(OrderService::class);
        return $orderService->removeRecordFromCart($request->headers->get('X-TOKEN'), $recordId);
    }

    public function changeAmountInCart(Request $request)
    {
        $recordId = $request->get('recordId');
        if (null === $recordId) {
            return response(['errors' => ['No Record ID provided']], 400);
        }

        $direction = $request->get('direction');
        if (null === $direction) {
            return response(['errors' => ['No direction provided']], 400);
        }

        /** @var OrderService $orderService */
        $orderService = app()->get(OrderService::class);
        return $orderService->changeOrderRecordAmount($request->headers->get('X-TOKEN'), $recordId, $direction);
    }

    public function order(Request $request)
    {
        $phone = $request->get('phone');
        if (null === $phone) {
            return response(['errors' => ['No phone provided']], 400);
        }

        $address = $request->get('address');
        if (null === $address) {
            return response(['errors' => ['No address provided']], 400);
        }

        /** @var OrderService $orderService */
        $orderService = app()->get(OrderService::class);
        $result = $orderService->closeOrder($request->headers->get('X-TOKEN'), $phone, $address);

        if (isset($result['errors']) && count($result['errors']) > 0) {
            return response(['errors' => $result['errors']], 400);
        } else {
            return response($result);
        }
    }

    public function history(Request $request)
    {
        /** @var OrderService $orderService */
        $orderService = app()->get(OrderService::class);
        $result = $orderService->getClosedOrdersByToken($request->headers->get('X-TOKEN'));

        return $result;
    }
}
