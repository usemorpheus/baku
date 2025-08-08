<div>
    Articles
    <ul>
        @foreach($models as $model)
            <li>
                <a href="{{route('articles.show', $model)}}">{{$model->title}}</a>
            </li>
        @endforeach
    </ul>
</div>
