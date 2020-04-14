<?php

namespace App\Service;

use App\Dish;
use App\Order;
use App\OrderDish;
use App\Token;
use App\User;

class OrderService
{
    use TokenTrait;

    public function addDishToOrder(string $dishId, string $token): array
    {
        /** @var Token $token */
        $token = $this->findToken($token);
        if (!$token) {
            return ['errors' => ['No token found']];
        }

        $dish = Dish::find($dishId);
        if (null === $dish) {
            return ['errors' => ['No dish found']];
        }

        /** @var Order $order */
        $order = $this->findOrCreateActiveOrderByToken($token);

        /** @var OrderDish $orderDish */
        $orderDish = $order->dishes()->where('dish_id', '=', $dish->id)->first();
        if ($orderDish) {
            $orderDish->inc();
            $orderDish->save();
        } else {
            $order->dishes()->insert([
                'order_id' => $order->id,
                'dish_id' => $dish->id
            ]);
        }

        return ['amount' => $order->dishes()->sum('amount')];
    }

    public function countDishesInOrder(string $token)
    {
        $token = $this->findToken($token);
        if (!$token) {
            return ['errors' => ['No token found']];
        }

        $order = $this->findOrCreateActiveOrderByToken($token);
        $amount = $order != null ? $order->dishes()->sum('amount') : 0;
        return ['amount' => $amount];
    }

    public function getActiveOrder(string $token)
    {
        $token = $this->findToken($token);
        if (!$token) {
            return ['errors' => ['No token found']];
        }

        $order = $this->findOrCreateActiveOrderByToken($token);
        $orderDishes = $order->dishes()->get();

        $totalCost = 0;
        $result = [
            'records' => $orderDishes->map(function ($od) use (&$totalCost) {
                $totalCost += $od->dish->cost * $od->amount;

                return [
                    'id' => $od->id,
                    'dish' => $od->dish,
                    'amount' => $od->amount,
                    'record_cost' => $od->amount * $od->dish->cost
                ];
            }),
        ];
        $result['totalCost'] = $totalCost;
        $result['dishesCount'] = $order->dishes()->sum('amount');

        return $result;
    }

    public function removeRecordFromCart(string $token, string $recordId)
    {
        $token = $this->findToken($token);
        if (!$token) {
            return ['errors' => ['No token found']];
        }

        $order = $this->findOrCreateActiveOrderByToken($token);
        $orderRecord = $order->dishes()->find($recordId);

        if ($orderRecord) {
            $orderRecord->delete();
        }

        return $this->getActiveOrder($token->token);
    }

    public function changeOrderRecordAmount(string $token, string $recordId, string $direction)
    {
        $token = $this->findToken($token);
        if (!$token) {
            return ['errors' => ['No token found']];
        }

        $order = $this->findOrCreateActiveOrderByToken($token);
        $orderRecord = $order->dishes()->find($recordId);

        if ($orderRecord) {
            if ('inc' === $direction) {
                $orderRecord->amount = $orderRecord->amount + 1;
                $orderRecord->save();
            } else {
                $orderRecord->amount = $orderRecord->amount - 1;
                if (0 === $orderRecord->amount) {
                    $orderRecord->delete();
                } else {
                    $orderRecord->save();
                }
            }
        }

        return $this->getActiveOrder($token->token);
    }

    public function closeOrder(string $token, string $phone, string $address)
    {
        $token = $this->findToken($token);
        if (!$token) {
            return ['errors' => ['No token found']];
        }

        $order = $this->findOrCreateActiveOrderByToken($token);
        $order->phone = $phone;
        $order->delivery_address = $address;
        $order->closed = true;
        $order->save();

        return $this->getActiveOrder($token->token);
    }

    public function getClosedOrdersByToken(string $token)
    {
        $token = $this->findToken($token);
        if (!$token) {
            return ['errors' => ['No token found']];
        }

        $user = $token->user;
        if (!$user) {
            return ['errors' => ['Token has no user']];
        }

        $orders = $user->orders()->closed()->get()->map(function ($o) use (&$totalAmount, &$totalCost) {
            $totalAmount = 0;
            $totalCost = 0;

            $order = [
                'phone' => $o->phone,
                'address' => $o->delivery_address
            ];

            $dishes = $o->dishes()->get()->map(function ($orderDish) use (&$totalAmount, &$totalCost) {
                $totalAmount += $orderDish->amount;
                $orderCost = $orderDish->dish->cost * $orderDish->amount;
                $totalCost += $orderCost;

                return [
                    'amount' => $orderDish->amount,
                    'cost' => $orderCost,
                    'dish' => $orderDish->dish
                ];
            });

            return [
                'id' => $o->id,
                'order' => $order,
                'dishes' => $dishes,
                'phone' => $o->phone,
                'address' => $o->delivery_address,
                'total_amount' => $totalAmount,
                'total_cost' => $totalCost
            ];
        });

        return $orders;
    }

    private function findOrCreateActiveOrderByToken(Token $token): Order
    {
        if ($user = $token->user) {
            $conditions = [
                ['holder_id', '=', $user->id],
                ['holder_type', '=', User::class]
            ];
        } else {
            $conditions = [
                ['holder_id', '=', $token->id],
                ['holder_type', '=', Token::class]
            ];
        }
        $order = Order::where($conditions)->active()->latest('id')->first();
        if (null === $order) {
            $order = new Order();
            if ($user = $token->user) {
                $order->holder_id = $user->id;
                $order->holder_type = User::class;
            } else {
                $order->holder_id = $token->id;
                $order->holder_type = Token::class;
            }
            $order->save();
        }

        return $order;
    }
}
