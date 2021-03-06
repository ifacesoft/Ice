<?php

namespace Ice\Action;

use Ice\Core\Logger;
use Ice\Helper\Date;
use Ice\Model\Token;
use Ice\Widget\Account_Form;

class Security_Password_Login_RegisterConfirm_Submit extends Security
{
    public function run(array $input)
    {
        /** @var Account_Form $accountForm */
        $accountForm = $input['widget'];

        $logger = $accountForm->getLogger();

        try {
            /** @var Token $token */
            $token = Token::createQueryBuilder()
                ->eq(['/' => $accountForm->getPart('token')->get('token')])
                ->gt('/expired', Date::get())
                ->getSelectQuery('*')
                ->getModel();

            // todo: получать токен без учета его истечения. срок истечения проверять отдельно + уже использован (used_at)

            if (!$token) {
                return [
                    'error' => $logger->info('Ключ подтверждения не найден или истек срок его действия', Logger::DANGER, true)
                ];
            }

            $this->registerConfirm($token, $input);

            return array_merge(
                ['success' => $logger->info('Регистрация успешно подтверждена', Logger::SUCCESS, true)],
                parent::run($input)
            );
        } catch (\Exception $e) {
            $logger->error($e->getMessage(), __FILE__, __LINE__, $e);

            return [
                'error' => $logger->info($e->getMessage(), Logger::DANGER, true)
            ];
        }
    }
}