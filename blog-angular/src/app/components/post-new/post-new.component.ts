import { Component, OnInit } from '@angular/core';
import { Router , ActivatedRoute, Params } from '@angular/router';
import { UserService } from '../../services/user.service';
import { Post } from '../../models/post'
import { CategoryService } from 'src/app/services/category.service';
import { global } from '../../services/global';
import { PostService} from '../../services/post.service';
import { Observable } from 'rxjs';


@Component({
  selector: 'app-post-new',
  templateUrl: './post-new.component.html',
  styleUrls: ['./post-new.component.css'],
  providers: [UserService, CategoryService, PostService]
})
export class PostNewComponent implements OnInit {

  public page_title : string;
  public identity;
  public token; 
  public post: Post;
  public categories;
  public status;
  public url;
  public resetVar = true;
  public is_edit: boolean;

  public afuConfig = {
    multiple: false,
    formatsAllowed: ".jpg,.png,.gif,.jpeg",
    maxSize: "50",
    uploadAPI:  {
      url: global.url+"post/upload",
      headers: {
        "Authorization" : this._userService.getToken()
      }
    },
    theme: "attachPin",
    hideProgressBar: false,
    hideResetBtn: true,
    hideSelectBtn: false,
    attachPinText: 'Sube tu avatar de usuario'
  };



  constructor(
    private _route :ActivatedRoute,
    private _router : Router,
    private _userService : UserService,
    private _categoryService : CategoryService,
    private _postService: PostService
  ) { 
    this.page_title = 'Crear Entrada';
    this.identity = this._userService.getIdentity();
    this.token = this._userService.getToken();
    this.is_edit = false;
  }

  ngOnInit(): void {
    //Methodo estandar de los componentes que se inicia al momento de iniciar la aplicacion (como JAVA)
    this.getCategories();
    
    //console.log(this.post);
    this.post = new Post(1,this.identity.sub, 1,'' , '',null,null);
  }

  
  
  getCategories(){
    this._categoryService.getCategories().subscribe(
      response =>{
        if(response.status == 'success'){
          this.categories = response.categories;
          console.log(this.categories);
        }
      },
      error =>{
        console.log(<any>error);
      }
    );
  }

  imageUpload(data){
    let image_data = JSON.parse(data.response);
    this.post.image = image_data.image ;
  }

  onSubmit(form){
    this._postService.create(this.token, this.post).subscribe(
      response =>{
        if(response.status == 'success'){
          this.post = response.post;
          this.status = 'success';
          this._router.navigate(['/inicio']);
        }else{
          this.status = 'error';
        }
      },
      error =>{
        console.log(error);
        this.status = 'error';
      }
    );
    }
}
