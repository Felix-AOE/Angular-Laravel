import { Component, OnInit } from '@angular/core';
import { Post } from '../../models/post'
import { PostService } from '../../services/post.service'
import { global} from '../../services/global'
import { UserService } from '../../services/user.service' 
import { Observable } from 'rxjs';


@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css'],
  providers:[UserService, PostService]
})
export class HomeComponent implements OnInit {
  public page_title: string;
  public url;
  public posts: Array<Post>;
  public identity;
  public token; 
  
  constructor(
    private _postService: PostService,
    private _userService: UserService
  ) {
    this.page_title = 'Inicio';
    this.url = global.url;
    this.identity = this._userService.getIdentity();
    //error al sacar el parametro token
    this.token = this._userService.getToken();
    
   }

  ngOnInit(): void {
    this.getPost();
  }

  getPost(){
    this._postService.getPosts().subscribe(
      response =>{
        if (response.status == 'success') {
          this.posts = response.posts;
          console.log(this.posts);
        }
      },
      error =>{
        console.log(error);
      }
    );
  }

  deletePost(id){
    this._postService.delete(this.token, id).subscribe(
      response =>{
        this.getPost();
      },
      error =>{
        console.log(error);
        console.log(this.posts)
      }
    );
  }
}
