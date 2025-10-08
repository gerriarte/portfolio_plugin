/**
 * JavaScript para el panel de administración del plugin Portfolio
 */

jQuery(document).ready(function($) {
    'use strict';
    
    // Variables globales
    let currentProjectId = null;
    let currentCategoryId = null;
    
    // Inicializar
    init();
    
    function init() {
        bindEvents();
        initColorPicker();
        initImageUpload();
        initGalleryUpload();
    }
    
    function bindEvents() {
        // Botones de agregar
        $('#add-new-project, #add-first-project').on('click', function() {
            openProjectModal();
        });
        
        $('#add-new-category, #add-first-category').on('click', function() {
            openCategoryModal();
        });
        
        // Botones de editar
        $(document).on('click', '.edit-project', function() {
            const projectId = $(this).data('project-id');
            editProject(projectId);
        });
        
        $(document).on('click', '.edit-category', function() {
            const categoryId = $(this).data('category-id');
            editCategory(categoryId);
        });
        
        // Botones de eliminar
        $(document).on('click', '.delete-project', function() {
            const projectId = $(this).data('project-id');
            deleteProject(projectId);
        });
        
        $(document).on('click', '.delete-category', function() {
            const categoryId = $(this).data('category-id');
            deleteCategory(categoryId);
        });
        
        // Modal events
        $('.modal-close, .modal-cancel').on('click', function() {
            closeModal();
        });
        
        $('.modal-save').on('click', function() {
            if ($(this).closest('#project-modal').length) {
                saveProject();
            } else if ($(this).closest('#category-modal').length) {
                saveCategory();
            }
        });
        
        // Cerrar modal con ESC
        $(document).on('keydown', function(e) {
            if (e.keyCode === 27) {
                closeModal();
            }
        });
        
        // Remover item de galería
        $(document).on('click', '.remove-gallery-item', function() {
            $(this).closest('.gallery-item').remove();
            updateGalleryField();
        });
        
        // Click fuera del modal
        $('.portfolio-modal').on('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    }
    
    function initColorPicker() {
        $('.color-preset').on('click', function() {
            const color = $(this).data('color');
            $('#category-color').val(color);
            $('.color-preset').removeClass('active');
            $(this).addClass('active');
        });
    }
    
    function updateGalleryField() {
        var galleryUrls = [];
        $('.gallery-preview .gallery-item').each(function() {
            var url = $(this).data('url');
            if (url) {
                galleryUrls.push(url);
            }
        });
        $('#project-gallery').val(JSON.stringify(galleryUrls));
        console.log('Campo galería actualizado:', galleryUrls);
    }
    
    function initImageUpload() {
        $('.upload-image-btn').on('click', function() {
            const button = $(this);
            const preview = button.siblings('.image-preview');
            const input = button.siblings('input[type="hidden"]');
            
            const frame = wp.media({
                title: portfolio_admin.strings.select_image || 'Seleccionar Imagen',
                button: {
                    text: portfolio_admin.strings.use_image || 'Usar esta imagen'
                },
                multiple: false
            });
            
            // Manejar z-index cuando se abre el media modal
            frame.on('open', function() {
                // Asegurar que el media modal esté por encima
                $('.media-modal').css('z-index', '1000000');
                $('.media-modal-backdrop').css('z-index', '999999');
                
                // Agregar clase al body para ajustar z-index
                $('body').addClass('portfolio-modal-open');
            });
            
            frame.on('close', function() {
                // Remover clase cuando se cierra
                $('body').removeClass('portfolio-modal-open');
            });
            
            frame.on('select', function() {
                const attachment = frame.state().get('selection').first().toJSON();
                input.val(attachment.url);
                preview.find('img').attr('src', attachment.url);
                preview.show();
            });
            
            frame.open();
        });
        
        $('.remove-image').on('click', function() {
            const preview = $(this).closest('.image-preview');
            const input = preview.siblings('input[type="hidden"]');
            input.val('');
            preview.hide();
        });
    }
    
    function initGalleryUpload() {
        $('.upload-gallery-btn').on('click', function() {
            const button = $(this);
            const preview = button.siblings('.gallery-preview');
            
            const frame = wp.media({
                title: 'Seleccionar Imágenes y Videos',
                button: {
                    text: 'Usar estos medios'
                },
                multiple: true,
                library: {
                    type: ['image', 'video']
                }
            });
            
            // Manejar z-index cuando se abre el media modal
            frame.on('open', function() {
                // Asegurar que el media modal esté por encima
                $('.media-modal').css('z-index', '1000000');
                $('.media-modal-backdrop').css('z-index', '999999');
                
                // Agregar clase al body para ajustar z-index
                $('body').addClass('portfolio-modal-open');
            });
            
            frame.on('close', function() {
                // Remover clase cuando se cierra
                $('body').removeClass('portfolio-modal-open');
            });
            
            frame.on('select', function() {
                const attachments = frame.state().get('selection').toJSON();
                preview.empty();
                
                var galleryUrls = [];
                
                attachments.forEach(function(attachment) {
                    var mediaUrl = attachment.url;
                    galleryUrls.push(mediaUrl);
                    
                    const mediaItem = $('<div class="gallery-item">').attr('data-url', mediaUrl);
                    
                    if (attachment.type === 'image') {
                        const img = $('<img>').attr('src', attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : mediaUrl);
                        mediaItem.append(img);
                    } else if (attachment.type === 'video') {
                        const video = $('<video controls>')
                            .attr('src', mediaUrl)
                            .attr('width', '100')
                            .attr('height', '80');
                        mediaItem.append(video);
                    }
                    
                    const removeBtn = $('<button type="button" class="remove-gallery-item">&times;</button>');
                    mediaItem.append(removeBtn);
                    preview.append(mediaItem);
                });
                
                // Actualizar campo hidden con las URLs
                $('#project-gallery').val(JSON.stringify(galleryUrls));
                console.log('Galería actualizada:', galleryUrls);
            });
            
            frame.open();
        });
    }
    
    function openProjectModal(projectId = null) {
        currentProjectId = projectId;
        
        if (projectId) {
            $('#modal-title').text('Editar Proyecto');
            loadProjectData(projectId);
        } else {
            $('#modal-title').text('Agregar Nuevo Proyecto');
            resetProjectForm();
        }
        
        $('#project-modal').fadeIn(300);
    }
    
    function openCategoryModal(categoryId = null) {
        currentCategoryId = categoryId;
        
        if (categoryId) {
            $('#category-modal-title').text('Editar Categoría');
            loadCategoryData(categoryId);
        } else {
            $('#category-modal-title').text('Agregar Nueva Categoría');
            resetCategoryForm();
        }
        
        $('#category-modal').fadeIn(300);
    }
    
    function closeModal() {
        $('.portfolio-modal').fadeOut(300);
        currentProjectId = null;
        currentCategoryId = null;
    }
    
    function loadProjectData(projectId) {
        // Cargar datos del proyecto desde el servidor
        $.ajax({
            url: portfolio_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'portfolio_get_project_for_edit',
                project_id: projectId,
                nonce: portfolio_admin.nonce
            },
            success: function(response) {
                if (response.success) {
                    const project = response.data;
                    
                    // Llenar formulario con datos del proyecto
                    $('#project-id').val(project.id);
                    $('#project-title').val(project.title);
                    $('#project-description').val(project.description);
                    $('#project-content').val(project.content);
                    $('#project-category').val(project.category_id);
                    $('#project-status').val(project.status);
                    $('#project-featured').prop('checked', project.featured == 1);
                    $('#project-url').val(project.external_url);
                    $('#project-date').val(project.project_date);
                    
                    // Cargar imagen destacada
                    if (project.featured_image) {
                        $('#project-image').val(project.featured_image);
                        $('.image-preview img').attr('src', project.featured_image);
                        $('.image-preview').show();
                    }
                    
                    // Cargar galería
                    if (project.gallery && project.gallery.length > 0) {
                        $('.gallery-preview').empty();
                        project.gallery.forEach(function(mediaUrl) {
                            var isVideo = mediaUrl.toLowerCase().match(/\.(mp4|webm|ogg|avi|mov)$/);
                            if (isVideo) {
                                $('.gallery-preview').append('<div class="gallery-item"><video src="' + mediaUrl + '" controls></video><button type="button" class="remove-gallery-item">&times;</button></div>');
                            } else {
                                $('.gallery-preview').append('<div class="gallery-item"><img src="' + mediaUrl + '" alt="Gallery"><button type="button" class="remove-gallery-item">&times;</button></div>');
                            }
                        });
                        // Guardar en campo hidden
                        $('#project-gallery').val(JSON.stringify(project.gallery));
                    }
                } else {
                    showNotification('Error al cargar los datos del proyecto', 'error');
                    resetProjectForm();
                }
            },
            error: function() {
                showNotification('Error al cargar los datos del proyecto', 'error');
                resetProjectForm();
            }
        });
    }
    
    function loadCategoryData(categoryId) {
        // Cargar datos de la categoría desde el servidor
        $.ajax({
            url: portfolio_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'portfolio_get_category_for_edit',
                category_id: categoryId,
                nonce: portfolio_admin.nonce
            },
            success: function(response) {
                if (response.success) {
                    const category = response.data;

                    // Llenar formulario con datos de la categoría
                    $('#category-id').val(category.id);
                    $('#category-name').val(category.name);
                    $('#category-description').val(category.description);
                    $('#category-color').val(category.color);

                    // Actualizar selector de color
                    $('.color-preset').removeClass('active');
                    $(`.color-preset[data-color="${category.color}"]`).addClass('active');
                } else {
                    showNotification('Error al cargar los datos de la categoría', 'error');
                    resetCategoryForm();
                }
            },
            error: function() {
                showNotification('Error al cargar los datos de la categoría', 'error');
                resetCategoryForm();
            }
        });
    }
    
    function resetProjectForm() {
        $('#project-form')[0].reset();
        $('#project-id').val('');
        $('.image-preview').hide();
        $('.gallery-preview').empty();
    }
    
    function resetCategoryForm() {
        $('#category-form')[0].reset();
        $('#category-id').val('');
        $('#category-color').val('#2196F3');
        $('.color-preset').removeClass('active');
        $('.color-preset[data-color="#2196F3"]').addClass('active');
    }
    
    function saveProject() {
        const formData = new FormData($('#project-form')[0]);
        formData.append('action', 'portfolio_save_project');
        formData.append('nonce', portfolio_admin.nonce);
        
        if (currentProjectId) {
            formData.append('project_id', currentProjectId);
        }
        
        // Procesar galería desde el campo hidden
        const galleryValue = $('#project-gallery').val();
        if (galleryValue) {
            try {
                const galleryArray = JSON.parse(galleryValue);
                galleryArray.forEach(function(url, index) {
                    formData.append('gallery[' + index + ']', url);
                });
            } catch(e) {
                console.error('Error al parsear galería:', e);
            }
        }
        
        $.ajax({
            url: portfolio_admin.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('.modal-save').prop('disabled', true).text(portfolio_admin.strings.saving);
            },
            success: function(response) {
                if (response.success) {
                    showNotification(portfolio_admin.strings.saved, 'success');
                    closeModal();
                    location.reload(); // Recargar para mostrar cambios
                } else {
                    showNotification(response.data.message || portfolio_admin.strings.error, 'error');
                }
            },
            error: function() {
                showNotification(portfolio_admin.strings.error, 'error');
            },
            complete: function() {
                $('.modal-save').prop('disabled', false).text('Guardar Proyecto');
            }
        });
    }
    
    function saveCategory() {
        const formData = new FormData($('#category-form')[0]);
        formData.append('action', 'portfolio_save_category');
        formData.append('nonce', portfolio_admin.nonce);
        
        if (currentCategoryId) {
            formData.append('category_id', currentCategoryId);
        }
        
        $.ajax({
            url: portfolio_admin.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('.modal-save').prop('disabled', true).text(portfolio_admin.strings.saving);
            },
            success: function(response) {
                if (response.success) {
                    showNotification(portfolio_admin.strings.saved, 'success');
                    closeModal();
                    location.reload(); // Recargar para mostrar cambios
                } else {
                    showNotification(response.data.message || portfolio_admin.strings.error, 'error');
                }
            },
            error: function() {
                showNotification(portfolio_admin.strings.error, 'error');
            },
            complete: function() {
                $('.modal-save').prop('disabled', false).text('Guardar Categoría');
            }
        });
    }
    
    function deleteProject(projectId) {
        if (!confirm(portfolio_admin.strings.confirm_delete)) {
            return;
        }
        
        $.ajax({
            url: portfolio_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'portfolio_delete_project',
                project_id: projectId,
                nonce: portfolio_admin.nonce
            },
            success: function(response) {
                if (response.success) {
                    showNotification('Proyecto eliminado exitosamente', 'success');
                    $(`.portfolio-project-card[data-project-id="${projectId}"]`).fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    showNotification(response.data.message || 'Error al eliminar el proyecto', 'error');
                }
            },
            error: function() {
                showNotification('Error al eliminar el proyecto', 'error');
            }
        });
    }
    
    function deleteCategory(categoryId) {
        if (!confirm(portfolio_admin.strings.confirm_delete)) {
            return;
        }
        
        $.ajax({
            url: portfolio_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'portfolio_delete_category',
                category_id: categoryId,
                nonce: portfolio_admin.nonce
            },
            success: function(response) {
                if (response.success) {
                    showNotification('Categoría eliminada exitosamente', 'success');
                    $(`.portfolio-category-card[data-category-id="${categoryId}"]`).fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    showNotification(response.data.message || 'Error al eliminar la categoría', 'error');
                }
            },
            error: function() {
                showNotification('Error al eliminar la categoría', 'error');
            }
        });
    }
    
    function editProject(projectId) {
        // Cargar datos del proyecto y abrir modal
        openProjectModal(projectId);
    }
    
    function editCategory(categoryId) {
        // Cargar datos de la categoría y abrir modal
        openCategoryModal(categoryId);
    }
    
    function showNotification(message, type = 'info') {
        const notification = $(`
            <div class="notice notice-${type} is-dismissible portfolio-notification">
                <p>${message}</p>
                <button type="button" class="notice-dismiss">
                    <span class="screen-reader-text">Descartar este aviso.</span>
                </button>
            </div>
        `);
        
        $('.wrap h1').after(notification);
        
        // Auto-dismiss después de 5 segundos
        setTimeout(function() {
            notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
        
        // Dismiss button
        notification.find('.notice-dismiss').on('click', function() {
            notification.fadeOut(300, function() {
                $(this).remove();
            });
        });
    }
});
