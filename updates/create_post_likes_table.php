<?php namespace RainLab\Forum\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;
use RainLab\Forum\Models\Post;
use RainLab\Forum\Models\TopicFollow;

class CreatePostLikesTable extends Migration
{
    public function up()
    {
        Schema::create('rainlab_forum_post_likes', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('post_id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->primary(['post_id', 'member_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('rainlab_forum_post_likes');
    }
}
