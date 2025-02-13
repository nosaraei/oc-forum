<?php namespace RainLab\Forum;

use Event;
use Backend;
use Nosaraei\User\Models\User;
use RainLab\Forum\Models\Member;
use System\Classes\PluginBase;
use Nosaraei\User\Controllers\Users as UsersController;

/**
 * Forum Plugin Information File
 */
class Plugin extends PluginBase
{
    public $require = ['Nosaraei.User'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'rainlab.forum::lang.plugin.name',
            'description' => 'rainlab.forum::lang.plugin.description',
            'author'      => 'Alexey Bobkov, Samuel Georges',
            'icon'        => 'icon-comments',
            'homepage'    => 'https://github.com/rainlab/forum-plugin'
        ];
    }

    public function boot()
    {
        User::extend(function($model) {
            $model->hasOne['forum_member'] = ['RainLab\Forum\Models\Member'];

            $model->bindEvent('model.beforeDelete', function() use ($model) {
                $model->forum_member && $model->forum_member->delete();
            });
        });

        UsersController::extendFormFields(function($widget, $model, $context) {
            // Prevent extending of related form instead of the intended User form
            if (!$widget->model instanceof \Nosaraei\User\Models\User) {
                return;
            }
            if ($context != 'update') {
                return;
            }
            if (!Member::getFromUser($model)) {
                return;
            }

//            $widget->addFields([
//                'forum_member[username]' => [
//                    'label'   => 'rainlab.forum::lang.settings.username',
//                    'tab'     => 'Forum',
//                    'comment' => 'rainlab.forum::lang.settings.username_comment'
//                ],
//                'forum_member[is_moderator]' => [
//                    'label'   => 'rainlab.forum::lang.settings.moderator',
//                    'type'    => 'checkbox',
//                    'tab'     => 'Forum',
//                    'span'    => 'auto',
//                    'comment' => 'rainlab.forum::lang.settings.moderator_comment'
//                ],
//                'forum_member[is_banned]' => [
//                    'label'   => 'rainlab.forum::lang.settings.banned',
//                    'type'    => 'checkbox',
//                    'tab'     => 'Forum',
//                    'span'    => 'auto',
//                    'comment' => 'rainlab.forum::lang.settings.banned_comment'
//                ]
//            ], 'primary');
        });

//        UsersController::extendListColumns(function($widget, $model) {
//            if (!$model instanceof \Nosaraei\User\Models\User) {
//                return;
//            }
//
//            $widget->addColumns([
//                'forum_member_username' => [
//                    'label'      => 'rainlab.forum::lang.settings.forum_username',
//                    'relation'   => 'forum_member',
//                    'select'     => 'username',
//                    'searchable' => false,
//                    'invisible'  => true
//                ]
//            ]);
//        });
    }

    public function registerComponents()
    {
        return [
           '\RainLab\Forum\Components\Channels'     => 'forumChannels',
           '\RainLab\Forum\Components\Channel'      => 'forumChannel',
           '\RainLab\Forum\Components\Topic'        => 'forumTopic',
           '\RainLab\Forum\Components\Topics'       => 'forumTopics',
           '\RainLab\Forum\Components\Posts'        => 'forumPosts',
           '\RainLab\Forum\Components\Member'       => 'forumMember',
           '\RainLab\Forum\Components\EmbedTopic'   => 'forumEmbedTopic',
           '\RainLab\Forum\Components\EmbedChannel' => 'forumEmbedChannel',
           '\RainLab\Forum\Components\RssFeed'      => 'forumRssFeed'
        ];
    }

    public function registerPermissions()
    {
        return [
            'rainlab.forum.manage_channels' => [
                'tab'   => 'rainlab.forum::lang.settings.channels',
                'label' => 'rainlab.forum::lang.settings.channels_desc'
            ]
        ];
    }
    
    public function registerNavigation()
    {
        return [
            'forum' => [
                'label' => 'rainlab.forum::lang.plugin.name',
                'url' => Backend::url('rainlab/forum/channels'),
                'icon' => 'icon-th',
                'permissions' => ['rainlab.forum.*'],
                'order' => 500,
                'sideMenu' => [
                    'channels' => [
                        'label' => 'rainlab.forum::lang.settings.channels',
                        'icon' => 'icon-th',
                        'url' => Backend::url('rainlab/forum/channels'),
                        'permissions' => ['rainlab.forum.manage_channels']
                    ]
                ]
            ]
        ];
    }
    
    public function registerSettings()
    {
        return [];
    }

    public function registerMailTemplates()
    {
        return [
            'rainlab.forum::mail.topic_reply'   => 'Notification to followers when a post is made to a topic.',
            'rainlab.forum::mail.member_report' => 'Notification to moderators when a member is reported to be a spammer.'
        ];
    }
    
    public function registerTargetTypes()
    {
        return [
            'forum' => [
                'label' => 'انجمن',
                'extendFields' => function(&$fields){
                    
                    $fields->main_value->hidden = true;
                    $fields->main_value->type = "dropdown";
                    
                },
                'description' => function($model){
                    
                    return "لینک به انجمن";
                }
            ]
        ];
    }
}
