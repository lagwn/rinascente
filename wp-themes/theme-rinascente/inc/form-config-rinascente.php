<?php
/**
 * Rinascente お問い合わせフォーム設定
 */

if (!defined('ABSPATH')) exit;

function rinascente_contact_form(): Rinascente_Form_Handler {

    $inquiry_type_options = [
        'YUMEHO の製品・導入に関するご相談',
        '資料請求（カタログ・仕様書）',
        'デモ・見学のご依頼',
        '事業提携・パートナーシップのご提案',
        '採用・求人について',
        '取材・メディア関係',
        'その他',
    ];

    if ( function_exists( 'rinascente_mica30_enabled' ) && rinascente_mica30_enabled() ) {
        array_splice( $inquiry_type_options, 1, 0, [ 'MICA30 の製品・導入に関するご相談' ] );
    }

    $fields = [
        'contact_name' => [
            'label'       => 'お名前',
            'type'        => 'text',
            'required'    => true,
            'placeholder' => '山田 太郎',
            'query_args'  => [
                'name',
            ],
        ],
        'organization' => [
            'label'       => '会社名・組織名',
            'type'        => 'text',
            'required'    => true,
            'placeholder' => '〇〇株式会社',
        ],
        'email' => [
            'label'       => 'メールアドレス',
            'type'        => 'email',
            'required'    => true,
            'placeholder' => 'example@company.jp',
        ],
        'tel' => [
            'label'       => '電話番号',
            'type'        => 'tel',
            'required'    => true,
            'placeholder' => '03-0000-0000',
        ],
        'inquiry_type' => [
            'label'    => 'お問い合わせ種別',
            'type'     => 'select',
            'required' => false,
            'options'  => $inquiry_type_options,
        ],
        'message' => [
            'label'       => 'お問い合わせ内容',
            'type'        => 'textarea',
            'required'    => false,
            'placeholder' => 'ご質問・ご要望をご自由にご記入ください。',
        ],
        'agree' => [
            'label'    => '個人情報の取扱いについて同意します',
            'type'     => 'checkbox',
            'required' => true,
        ],
    ];

    $form = new Rinascente_Form_Handler('rinascente_contact', $fields);

    $company = get_theme_mod('company_name', '株式会社Rinascente');
    $form->set_mail_config([
        'admin_email'        => get_option('admin_email'),
        'from_name'          => $company,
        'from_email'         => get_option('admin_email'),
        'subject'            => "【{$company}】お問い合わせがありました",
        'auto_reply_subject' => "【{$company}】お問い合わせありがとうございます",
    ]);

    return $form;
}
