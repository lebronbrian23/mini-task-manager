<x-mail::message>
# Task completed

Your task has been marked as completed.

<x-mail::button :url="$url" color="success">
View Task
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
