import { Component, OnInit } from '@angular/core';

import { Router, ActivatedRoute, Params } from '@angular/router';
import { Post } from '../../models/post';
import { PostService } from '../../services/post.service';
import { global} from '../../services/global';
import { UserService } from '../../services/user.service';
import { User } from '../../models/user';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css'],
  providers:[UserService, PostService]
})
export class ProfileComponent implements OnInit {

  public url;
  public posts: Array<Post>;
  public identity;
  public token; 
  public user: User;

  constructor(
    private _postService: PostService,
    private _userService: UserService,
    private _route: ActivatedRoute,
    private _router: Router
  ) {
    this.url = global.url;
    this.identity = this._userService.getIdentity();
    this.token = this._userService.getToken();
   }

  ngOnInit(): void {
    this.getProfile();
  }

  getUser(userId){
    this._userService.getUser(userId).subscribe(
      response =>{
        if (response.status == 'success') {
          this.user = response.user;
        }
      },
      error =>{
        console.log(error);
      }
    );
  }


  getProfile(){
    //Sacar el id del post de la url 
    this._route.params.subscribe(params =>{// se crea una funcion de callback para obtener los parametros de la url 
      let userId =+ params['id'];//se saca la id de params
      this.getUser(userId);
      this.getPosts(userId);
    });
  }

  getPosts(userId){
    this._userService.getPosts(userId).subscribe(
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
        this.getProfile();
      },
      error =>{
        console.log(error);
        console.log(this.posts)
      }
    );
  }
}
