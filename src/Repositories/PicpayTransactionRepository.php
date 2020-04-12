<?php

namespace Cagartner\Picpay\Repositories;

use Cagartner\Picpay\Contracts\PicpayTransaction;
use Webkul\Core\Eloquent\Repository;

class PicpayTransactionRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return PicpayTransaction::class;
    }

    /**
     * @param array $data
     * @return PicpayTransaction
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * @param  array  $data
     * @param  int  $id
     * @param  string  $attribute
     * @return PicpayTransaction
     */
    public function update(array $data, $id, $attribute = "reference_id")
    {
        $transaction = $this->findOneByField($attribute, $id);
        $transaction->update($data);
        return $transaction;
    }
}