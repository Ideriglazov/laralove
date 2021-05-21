<div class="form-group">
    <input name="title" type="text" class="form-control" required value="{{ old('title') ?? $post->title ?? '' }}"><!--при возникшей ошибке во время создании поста команда old('title') не дает исчезнуть введенному в форму тексту-->
</div>

<div class="form-group">
    <textarea name="description" rows="3" class="form-control" required>{{ old('title') ?? $post->description ?? ''}}</textarea>
</div>

<div class="form-group">
    <input type="file" name="img">
</div>
