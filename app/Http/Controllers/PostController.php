<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
//use宣言は外部にあるクラスをPostController内にインポートできる。
//この場合、App\Models内のPostクラスをインポートしている。
use App\Models\Post;
use App\Models\Category;


class PostController extends Controller
{
    //
    /**
     * Post一覧を表示する
     * 
     * @param Post Postモデル
     * @return array Postモデルリスト
     */
    public function index(Post $post)//インポートしたPostをインスタンス化して$postとして使用。
    {
        //クライアントインスタンス生成
        $client = new \GuzzleHttp\Client();
        
        //GET通信するURL
        $url = 'https://teratail.com/api/v1/questions';
        
        //リクエスト送信と返却データの取得
        //Bearerトークンにアクセストークンを指定して認証を行う
        $response = $client->request(
            'GET',
            $url,
            ['Bearer' => config('services.teratail.token')]
        );
        
        
        //API通信で取得したデータはjson形式なので
        //PHPファイルに対応した連想配列にデコードする
        $questions = json_decode($response->getBody(), true);
        
        //index bladeに取得したデータを渡す
        return view('posts/index')->with([
            //見本だとview('index')になっているが、
            //エラーになるので'posts/index'にしてある。ルーティングが原因？
            'posts' => $post->getPaginateByLimit(),
            'questions' => $questions['questions'],
        ]);
        
        //return view('posts/index')->with(['posts' => $post->getPaginateByLimit(5)]);
    }
    
    public function show(Post $post)
    {
        return view('posts/show')->with(['post' => $post]);
    }
    
    public function create(Category $category)
    {
        return view('posts/create')->with(['categories' => $category->get()]);
    }
    
    public function store(PostRequest $request, Post $post)
    {
        $input = $request['post'];
        $post->fill($input)->save();
        return redirect('/posts/' . $post->id);
    }
    
    public function edit(Post $post)
    {
        return view('posts/edit')->with(['post'=> $post]);
    }
    
    public function update(PostRequest $request, Post $post)
    {
        $input_post = $request['post'];
        $post->fill($input_post)->save();
        return redirect('/posts/' . $post->id);
    }
    
    public function delete(Post $post)
    {
        $post->delete();
        return redirect('/');
    }
}
