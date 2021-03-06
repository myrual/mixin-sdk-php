<?php
/**
 * Created by PhpStorm.
 * User: kurisu
 * Date: 18-11-16
 * Time: 下午10:05
 */

namespace ExinOne\MixinSDK;

use ExinOne\MixinSDK\Exceptions\MixinNetworkRequestException;

/**
 * @see \ExinOne\MixinSDK\Apis\Pin
 * @method  array updatePin($oldPin, $pin): array
 * @method  array verifyPin($pin): array
 *
 * @see \ExinOne\MixinSDK\Apis\User
 * @method  array readProfile(): array
 * @method  array updateProfile(string $full_name, string $avatar_base64 = ''): array
 * @method  array updatePreferences(string $receive_message_source, string $accept_conversation_source): array
 * @method  array rotateQRCode(): array
 * @method  array readFriends(): array
 *
 * @see \ExinOne\MixinSDK\Apis\Network
 * @method  array readUser(string $userId): array
 * @method  array readUsers(array $userIds): array
 * @method  array searchUser($item): array
 * @method  array readNetworkAsset(string $assetId): array
 * @method  array readNetworkSnapshots(int $limit = null, string $offset = null, string $asset = '', string $order = 'DESC'): array
 * @method  array readNetworkSnapshot(string $snapshotId): array
 * @method  array createUser(string $fullName): array
 * @method  array externalTransactions(string $asset = null, string $public_key = null, int $limit = null, string $offset = null, string $account_name = null): array
 * @method  array createAttachments(): array
 * @method  array mixinNetworkChainsSyncStatus(): array
 * @method  array topAsset(): array
 *
 * @see \ExinOne\MixinSDK\Apis\Wallet
 * @method  array readAssets(): array
 * @method  array readAsset(string $assetId): array
 * @method  array deposit(string $assetId): array
 * @method  array withdrawal(string $addressId, $amount, string $memo, $pin, $tracd_id = null): array
 * @method  array transfer(string $assetId, string $opponentId, $pin, $amount, string $memo, $tracd_id = null): array
 * @method  array verifyPayment(string $asset_id, string $opponent_id, $amount, string $trace_id): array
 * @method  array readTransfer(string $traceId): array
 * @method  array createAddress(string $assetId, string $publicKey, $pin, string $label, bool $isEOS = false): array
 * @method  array readAddress(string $addressId): array
 * @method  array readAddresses(string $assetId): array
 * @method  array deleteAddress(string $addressId, $pin): array
 * @method  array readAssetFee(string $assetId): array
 * @method  array readUserSnapshots($limit = null, string $offset = null, string $asset = '', string $order = 'DESC'): array
 * @method  array readUserSnapshot(string $snapshotId): array
 *
 * @see \ExinOne\MixinSDK\Apis\Pin
 * @see \ExinOne\MixinSDK\Apis\Wallet
 * @see \ExinOne\MixinSDK\Apis\Network
 * @see \ExinOne\MixinSDK\Apis\Wallet
 *
 * @throws \ExinOne\MixinSDK\Exceptions\MixinNetworkRequestException
 */
class Container
{
    protected $detailClass;

    protected $raw = false;

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     * @throws \ExinOne\MixinSDK\Exceptions\MixinNetworkRequestException
     */
    public function __call($name, $arguments)
    {
        $this->detailClass->init($name);

        // 调用对象的$name 方法,获得需要发送的 header 和 body
        ['content' => $content, 'customize_res' => $customize_res]
            = call_user_func_array([$this->detailClass, $name], $arguments);

        if (! $this->isRaw() && ($content['error'] ?? 0)) {
            // 出现异常
            $error = $content['error'];
            $this->boomRoom($error['code'], $error['description']);
        } elseif ($this->isRaw()) {
            return array_merge($content ?? [], $customize_res);
        } else {
            return array_merge($content['data'] ?? [], $customize_res);
        }
    }

    /**
     * @param $detailClass
     *
     * @return $this
     */
    public function setDetailClass($detailClass)
    {
        $this->detailClass = $detailClass;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDetailClass()
    {
        return $this->detailClass;
    }

    /**
     * @param bool $raw
     */
    public function setRaw(bool $raw): void
    {
        $this->raw = $raw;
    }

    /**
     * @return bool
     */
    public function isRaw(): bool
    {
        return $this->raw;
    }

    /**
     * @param int $timeout
     *
     * @return $this
     */
    public function setTimeout(int $timeout)
    {
        $this->detailClass->setTimeout($timeout);

        return $this;
    }

    /**
     * @param $iterator
     *
     * @return $this
     */
    public function setIterator(array $iterator)
    {
        $this->detailClass->setIterator($iterator);

        return $this;
    }

    /**
     * 爆炸室
     *
     * @param $errorCode
     * @param $description
     *
     * @throws \ExinOne\MixinSDK\Exceptions\MixinNetworkRequestException
     */
    public function boomRoom($errorCode, $description)
    {
        throw new MixinNetworkRequestException($description, $errorCode);
    }
}
