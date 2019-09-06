<div class="question-answer-section">
    <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.measure-pages.comments')}}</p>
    @if(array_key_exists($stepSlug, $commentsByStep))
        @foreach($commentsByStep[$stepSlug] as $inputSourceName => $commentsCategorizedUnderColumn)
            {{-- The column can be a category, this will be the case when the comment is stored under a catergory --}}
            @foreach($commentsCategorizedUnderColumn as $columnOrCategory => $comment)
                <div class="question-answer">
                    @if(is_array($comment))
                        @foreach($comment as $column => $c)
                            <p class="w-320">{{$inputSourceName}} ({{$columnOrCategory}})</p>
                            <p>{{$c}}</p>
                        @endforeach
                    @else
                        <p class="w-320">{{$inputSourceName}}</p>
                        <p>{{$comment}}</p>
                    @endif
                </div>
            @endforeach
        @endforeach
    @endif
</div>