<div class="question-answer-section">
    <p class="lead">{{$title ?? __('pdf/user-report.measure-pages.comments.title')}}</p>
    @foreach($comments as $inputSourceName => $comment)
        {{-- The column can be a category, this will be the case when the comment is stored under a catergory --}}
        <table class="full-width">
            <tbody>
                @if(is_array($comment))
                    @foreach($comment as $short => $comment)
                        @if(!empty($comment))
                            <tr class="h-20">
                                <td class="w-100">{{$inputSourceName}} (@lang("pdf/user-report.measure-pages.comments.short-translations.{$short}"))</td>
                                <td>{!!  nl2br($comment, '<br>')!!}</td>
                            </tr>
                        @endif
                    @endforeach
                @else
                <tr class="h-20">
                    <td class="w-100">{{$inputSourceName}}</td>
                    <td>{!!  nl2br($comment, '<br>')!!}</td>
                </tr>
                @endif
            </tbody>
        </table>
    @endforeach
</div>
