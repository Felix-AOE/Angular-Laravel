<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;

class PruebasController extends Controller
{
    public function testOrm() {
        /*
        $posts = Post::all();//Hacer un SELECT * FROM
        foreach ($posts as $post){ //en cada iteracion creame un post
            echo "<h1>".$post->title."</h1>";
            echo "<span>{$post->user->name} - {$post->category->name}</span>"; //a los post agarras el metodo user y de usuarios muestas el parametro name 
            echo "<p>".$post->content."</p>";
            echo "<hr>";
        }
        */
        $categories = Category::all();
        foreach ($categories as $category ) {
            echo "<h1>".$category->name."</h1>" ;
            
            foreach ($category->post as $post) {
                echo "<h3>".$post->title."<h3>";
                echo "<span>".$post->user->name."-".$post->category->name ."span";
                echo "<p>".$post->content."</p>";                      
            }
            echo "<hr>";
        }
        
        
        die();
    }
    
    
    
}
