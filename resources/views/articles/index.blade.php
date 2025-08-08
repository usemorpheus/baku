<div>
    Articles
    <ul>
        @foreach($models as $model)
            <li>
                <a href="{{route('news.show', $model)}}">{{$model->title}}</a>
            </li>
        @endforeach
    </ul>
</div>
