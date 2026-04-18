<?php
/**
 * YUMEHO お問い合わせフォーム設定
 */

if (!defined('ABSPATH')) exit;

function yumeho_contact_form(): Rinascente_Form_Handler {

    $fields = [
        'inquiry_type' => [
            'label'    => 'お問い合わせ内容',
            'type'     => 'radio',
            'required' => true,
            'query_args' => [
                'tmptype',
            ],
            'options'  => [
                '資料請求',
                '導入・見積相談',
                'デモ体験のご依頼',
                '現地調査のご依頼',
                'その他',
            ],
        ],
        'facility' => [
            'label'       => '施設名・医療機関名',
            'type'        => 'text',
            'required'    => true,
            'placeholder' => '例：医療法人社団○○会 ○○病院',
        ],
        'facility_type' => [
            'label'    => '施設種別',
            'type'     => 'radio',
            'required' => true,
            'options'  => [
                '病院',
                '介護老人保健施設',
                'デイサービス',
                'その他',
            ],
        ],
        'role' => [
            'label'    => 'あなたのお立場',
            'type'     => 'radio',
            'required' => true,
            'options'  => [
                '理学療法士 / 作業療法士',
                '施設長 / 事務長',
                '購買担当 / 設備委員',
                '介護職員',
                'その他',
            ],
        ],
        'contact_name' => [
            'label'       => '担当者様 氏名',
            'type'        => 'text',
            'required'    => true,
            'placeholder' => '例：山田 太郎',
            'query_args'  => [
                'name',
            ],
        ],
        'email' => [
            'label'       => 'メールアドレス',
            'type'        => 'email',
            'required'    => true,
            'placeholder' => '例：yamada@example.com',
        ],
        'tel' => [
            'label'       => '電話番号',
            'type'        => 'tel',
            'required'    => true,
            'placeholder' => '例：03-1234-5678',
        ],
        'message' => [
            'label'       => 'ご質問・ご要望など',
            'type'        => 'textarea',
            'required'    => false,
            'placeholder' => 'ご自由にご記入ください。',
            'query_args'  => [
                'msg',
            ],
        ],
    ];

    $form = new Rinascente_Form_Handler('yumeho_contact', $fields);

    $site_name = get_bloginfo('name') ?: 'YUMEHO';
    $form->set_mail_config([
        'admin_email'        => get_option('admin_email'),
        'from_name'          => $site_name,
        'from_email'         => get_option('admin_email'),
        'subject'            => "【{$site_name}】お問い合わせがありました",
        'auto_reply_subject' => "【{$site_name}】お問い合わせありがとうございます",
    ]);

    return $form;
}
