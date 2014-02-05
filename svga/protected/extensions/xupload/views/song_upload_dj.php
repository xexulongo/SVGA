<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        
        <td class="name"><span>{%=file.name%}</span></td>
        <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
        {% if (file.error) { %}
            <td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
        {% } else if (o.files.valid && !i) { %}
            <td><table>
                <tr>
                    <td>
                        <input type="text" placeholder="<?= Yii::t('engrescat', 'Títol') ?>" name="Song[name]" class="form-control" required>
                    </td>
                    <td>
                        <input type="text" placeholder="<?= Yii::t('engrescat', 'Artista') ?>" name="Song[author]" class="form-control" required>
                    </td>
                    <td>
                        <!--<div class="progress progress-success progress-striped active"><div class="progress-bar" style="width:0%;"></div></div>-->
                        <img class="working" style="display: none" src="<?= Yii::app()->baseUrl?>/img/spinner.gif" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p>Tracklist</p>
                        <textarea rows="7" class="form-control" name="Song[tracklist]" required></textarea>
                    </td>
                </tr>
            </table></td>
            <td class="start">{% if (!o.options.autoUpload) { %}
                <button class="btn btn-primary">
                    <i class="icon-upload icon-white"></i>
                    <span>{%=locale.fileupload.start%}</span>
                </button>
            {% } %}</td>
        {% } else { %}
            <td colspan="2"></td>
        {% } %}
        <td class="cancel">{% if (!i) { %}
            <button class="btn btn-warning">
                <i class="icon-ban-circle icon-white"></i>
                <span>{%=locale.fileupload.cancel%}</span>
            </button>
        {% } %}</td>
    </tr>
{% } %}
</script>
