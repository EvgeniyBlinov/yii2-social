[![MIT License][license-image]][license-url]

# yii2-social
yii2-social

## Usage

 Add to `composer.json`:

```json
    "require": {
        "cent/yii2-social"             : "v0.0.3"
    },
    "repositories": [
        {
            "type": "git",
            "url" : "https://github.com/EvgeniyBlinov/yii2-social"
        }
    ],
```

Main config:

```php
        'twitter' => [
            'class'              => 'cent\yii2social\components\Twitter',
            'consumer_key'       => APP_SOCIAL_TW_CKEY,
            'consumer_secret'    => APP_SOCIAL_TW_CSECRET,
            'oauth_token'        => APP_SOCIAL_TW_OATOKEN,
            'oauth_token_secret' => APP_SOCIAL_TW_OASECRET,
            'oAuthClass'         => 'common\components\TwitterOAuth',
            //'callback'           => 'YOUR_TWITTER_CALLBACK_URL',
        ],
        'facebook' => [
            'class'              => 'cent\yii2social\components\Facebook',
            'appId'              => APP_SOCIAL_FB_ID,
            'appSecret'          => APP_SOCIAL_FB_SECRET,
            'oAToken'            => APP_SOCIAL_FB_OATOKEN,
        ],

        'social' => [
            'class'     => 'cent\yii2social\components\SocialComponent',
            'resources' => [
                'cent\yii2social\mappers\Twitter' => [
                    'model' => 'common\models\SocialPost',
                ],
                'cent\yii2social\mappers\Facebook' => [
                    'model' => 'common\models\SocialPost',
                ],
            ],
            'options'   => [
                'sort'  => 'created_at',
                'order' => 'ASC',
            ],
        ],
```

Model:

```php

use cent\yii2social\interfaces\ISocialModel;

class SocialPost extends SocialPostBase implements ISocialModel
{
    /**
     * @return array
     **/
    public function getMappedFields()
    {
        return [
            'twitter' => [
                'created_at'     => 'created_at',
                '_social_name'   => 'social',
                'id:string'             => 'post_id',
                'lang'           => 'lang',
                'text'           => 'text',
                'user'           => 'author_data',
                'retweet_count'  => 'repost_count',
                'favorite_count' => 'favourites_count',
            ],
            'facebook' => [
                'created_time'     => 'created_at',
                '_social_name'     => 'social',
                // type
                'id:string'        => 'post_id',
                'message'          => 'text',
                'from'             => 'author_data',
                // deep level
                'shares.count'     => 'repost_count',
            ],
        ];
    }

    // ...
```

Action in controller:

```php
    /**
     * Lists all SocialPost models.
     * @return mixed
     */
    public function actionGetPosts()
    {
        Yii::$app->response->format = 'json';
        $status                     = true;
        $errorMsg                   = '';
        // get hashtags from database
        if ($hashtags = array_map(function ($model){
            return $model->hashtag;
        }, (array) SocialHashtag::find()->where(['status' => 1])->all())) {
             $socialPosts = Yii::$app->social->getMixedData([
                'twitter' => [
                    'getByHashtags' => [
                        $hashtags,
                        [
                            'urlOptions' => [
                                'lang'   => 'en',
                                'locale' => 'en',
                            ],
                        ],
                    ],
                ],
                'facebook' => [
                    'get' => [
                        '/' . APP_SOCIAL_FB_PAGE_ID,
                        [
                            'urlOptions' => ['fields' => 'posts.limit(20)'],
                            'preFormatter' => function ($response){
                                return json_decode(json_encode($response), true);
                            },
                        ]
                    ],
                ],
            ]);

            foreach ($socialPosts as $post) {
                $model = new SocialPost();
                $model->setAttributes($post);
                $status = (boolean) $model->save();
                if (!$status) {
                    $errorMsg = array_pop($model->getFirstErrors());
                    break;
                }
            }
        } else {
            $errorMsg = 'Hashtags not found!';
        }
        if (!$status) {
            return [
                'error' => true,
                'errorMsg' => $errorMsg,
            ];
        } else {
            return ['success' => true];
        }
    }
```


## License

[![MIT License][license-image]][license-url]

## Author

- [Blinov Evgeniy](mailto:evgeniy_blinov@mail.ru) ([http://blinov.in.ua/](http://blinov.in.ua/))

[license-image]: http://img.shields.io/badge/license-MIT-blue.svg?style=flat
[license-url]: LICENSE
