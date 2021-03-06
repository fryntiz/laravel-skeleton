<div class="row">
    <div class="col-md-12 text-center mt-3">
        <h2>
            SEO
        </h2>
    </div>

    <div class="col-md-12 form-row mt-4">
        <h3>Título</h3>
        &nbsp;<small>Max. 55 carácteres.</small>

        {!!
            FormHelper::inputText('og_title', $seo->og_title, [
                'placeholder' => 'Título para mostrar en redes sociales',
                'maxlength' => 55,
                'data-error' => isset($errors) && $errors->has('og_title'),
                'data-error_message' => isset($errors) && $errors->has('og_title') ? $errors->first('og_title') : '',
            ])
        !!}
    </div>

    <div class="col-md-12 form-row mt-4">
        <h3>Descripción</h3>
        &nbsp;<small>Max. 115 carácteres en móvil y 155 en desktop.</small>

        {!!
            FormHelper::inputText('description', $seo->description, [
                'placeholder' => 'Descripción de la entrada',
                'maxlength' => 155,
                'data-error' => isset($errors) && $errors->has('description'),
                'data-error_message' => isset($errors) && $errors->has('description') ? $errors->first('description') : '',
            ])
        !!}
    </div>

    <div class="col-md-12 form-row mt-4">
        <h3>Keywords</h3>

        <br />

        {!!
            FormHelper::inputText('keywords', $seo->keywords, [
                'placeholder' => 'diseño,programación,desarrollo',
                'maxlength' => 500,
                'data-error' => isset($errors) && $errors->has('keywords'),
                'data-error_message' => isset($errors) && $errors->has('keywords') ? $errors->first('keywords') : '',
            ])
        !!}
    </div>

    {{-- Opciones de indexado y/o seguimiento de enlaces --}}
    <div class="col-md-12 form-row mt-4">
        <h3>Indexación y seguimiento de enlaces</h3>

        <select class="form-control" name="robots">
            @foreach([
                'index' => 'Index',
                'noindex' => 'No Index',
                'follow' => 'Follow',
                'nofollow' => 'No Follow',
                'index,nofollow' => 'Index, No Follow',
                'index,follow' => 'Index, Follow',
                'noindex,nofollow' => 'No Index, No Follow',
            ] as $idx => $ele)
                <option value="{{$idx}}" {{$idx == $seo->robots ? 'selected' : ''}}>
                    {{$ele}}
                </option>
            @endforeach
        </select>
    </div>

    <hr />

    <div class="col-md-12 form-row mt-4">
            <h3>Redes Sociales - Descripción</h3>

            {!!
                FormHelper::inputText('og_description', $seo->og_description, [
                    'placeholder' => 'Descripción',
                    'maxlength' => 500,
                    'data-error' => isset($errors) && $errors->has('og_description'),
                'data-error_message' => isset($errors) && $errors->has('og_description') ? $errors->first('og_description') : '',
                ])
            !!}
    </div>

    <div class="col-md-12 form-row mt-4">
        <h3>Redes Sociales - Texto alternativo a imagen en Redes Sociales</h3>

        {!!
            FormHelper::inputText('og_image_alt', $seo->og_image_alt, [
                'placeholder' => 'Image Alt',
                'maxlength' => 500,
                'data-error' => isset($errors) && $errors->has('og_image_alt'),
                'data-error_message' => isset($errors) && $errors->has('og_image_alt') ? $errors->first('og_image_alt') : '',
            ])
        !!}
    </div>

    <div class="col-md-12 form-row mt-4">
        <div class="form-text col-12">
            <h3>Redes Sociales - Image</h3>

            <br />

            <span class="text-center">
                <img src="{{$seo ? $seo->urlImage : (new \App\ContentSeo)->urlImage}}"
                     alt="Image Content"
                     title="Image Content"
                     class="form-image-preview text-center" />

                <br />

                <input type="file"
                       name="og_image"
                       accept="image/*"
                       class="form-control-file mt-3 mb-3 text-center" />
            </span>
        </div>
    </div>
</div>
