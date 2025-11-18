/**
 * SISTEMA DE CHAMADOS - SABORES REFEITÓRIO
 * JavaScript Principal
 */

$(document).ready(function() {
    // ========================================
    // CONFIGURAÇÕES GERAIS
    // ========================================

    // Auto-hide alerts após 5 segundos
    setTimeout(function() {
        $('.alert').not('.alert-permanent').fadeOut('slow');
    }, 5000);

    // Tooltip Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Popover Bootstrap
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // ========================================
    // MÁSCARAS DE INPUT
    // ========================================

    // Máscara de telefone
    $('input[type="tel"], input[name*="telefone"]').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');

        if (value.length <= 10) {
            // (XX) XXXX-XXXX
            value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
            value = value.replace(/(\d)(\d{4})$/, '$1-$2');
        } else {
            // (XX) XXXXX-XXXX
            value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
            value = value.replace(/(\d)(\d{4})$/, '$1-$2');
        }

        $(this).val(value);
    });

    // Máscara de CNPJ
    $('input[name*="cnpj"]').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');

        // XX.XXX.XXX/XXXX-XX
        value = value.replace(/^(\d{2})(\d)/, '$1.$2');
        value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
        value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');

        $(this).val(value);
    });

    // ========================================
    // CONFIRMAÇÕES
    // ========================================

    // Confirmação de exclusão
    $('.btn-delete, .btn-deletar').on('click', function(e) {
        if (!confirm('Tem certeza que deseja excluir? Esta ação não pode ser desfeita.')) {
            e.preventDefault();
            return false;
        }
    });

    // Confirmação de finalização
    $('.btn-finalizar').on('click', function(e) {
        if (!confirm('Tem certeza que deseja finalizar este chamado?')) {
            e.preventDefault();
            return false;
        }
    });

    // ========================================
    // UPLOADS
    // ========================================

    // Preview de imagens antes do upload
    $('input[type="file"]').on('change', function() {
        const files = this.files;
        const preview = $(this).siblings('.file-preview');

        if (preview.length && files.length) {
            preview.html('');

            for (let i = 0; i < files.length; i++) {
                const file = files[i];

                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.append(`
                            <div class="preview-image">
                                <img src="${e.target.result}" class="img-thumbnail" style="max-width: 100px; max-height: 100px; margin: 5px;">
                            </div>
                        `);
                    };

                    reader.readAsDataURL(file);
                } else {
                    preview.append(`
                        <div class="preview-file">
                            <span class="badge bg-secondary">${file.name}</span>
                        </div>
                    `);
                }
            }
        }

        // Mostrar nome do arquivo no label
        const label = $(this).next('label');
        if (label.length && files.length) {
            if (files.length === 1) {
                label.text(files[0].name);
            } else {
                label.text(`${files.length} arquivos selecionados`);
            }
        }
    });

    // Validação de tamanho de arquivo
    $('input[type="file"]').on('change', function() {
        const maxSize = 5 * 1024 * 1024; // 5MB
        const files = this.files;

        for (let i = 0; i < files.length; i++) {
            if (files[i].size > maxSize) {
                alert(`O arquivo ${files[i].name} é muito grande. Tamanho máximo: 5MB`);
                $(this).val('');
                return false;
            }
        }
    });

    // ========================================
    // FORMULÁRIOS
    // ========================================

    // Validação de formulário antes de enviar
    $('form').on('submit', function(e) {
        const form = $(this);

        // Verificar campos obrigatórios
        let valid = true;
        form.find('[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                valid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!valid) {
            e.preventDefault();
            alert('Por favor, preencha todos os campos obrigatórios.');
            return false;
        }
    });

    // Remover classe de erro ao digitar
    $('input, textarea, select').on('input change', function() {
        $(this).removeClass('is-invalid');
    });

    // ========================================
    // DATATABLE CONFIGURAÇÕES
    // ========================================

    // Configuração padrão do DataTables em português
    if ($.fn.DataTable) {
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            },
            pageLength: 25,
            responsive: true,
            order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: -1 } // Última coluna (ações) não ordenável
            ]
        });
    }

    // ========================================
    // SIDEBAR MOBILE
    // ========================================

    // Toggle sidebar em mobile
    $('.sidebar-toggle').on('click', function() {
        $('.sidebar').toggleClass('active');
    });

    // Fechar sidebar ao clicar fora (mobile)
    $(document).on('click', function(e) {
        if ($(window).width() < 768) {
            if (!$(e.target).closest('.sidebar, .sidebar-toggle').length) {
                $('.sidebar').removeClass('active');
            }
        }
    });

    // ========================================
    // BUSCA EM TEMPO REAL
    // ========================================

    // Busca em tabelas
    $('#busca-tabela').on('keyup', function() {
        const value = $(this).val().toLowerCase();

        $('table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // ========================================
    // CONTADOR DE CARACTERES
    // ========================================

    $('textarea[maxlength]').each(function() {
        const maxLength = $(this).attr('maxlength');
        const counter = $(`<small class="text-muted float-end">0 / ${maxLength}</small>`);

        $(this).after(counter);

        $(this).on('input', function() {
            const length = $(this).val().length;
            counter.text(`${length} / ${maxLength}`);

            if (length >= maxLength * 0.9) {
                counter.addClass('text-warning');
            } else {
                counter.removeClass('text-warning');
            }
        });
    });

    // ========================================
    // COPIAR PARA CLIPBOARD
    // ========================================

    $('.btn-copy').on('click', function() {
        const text = $(this).data('copy');

        navigator.clipboard.writeText(text).then(function() {
            alert('Copiado para a área de transferência!');
        }).catch(function() {
            alert('Erro ao copiar. Tente novamente.');
        });
    });

    // ========================================
    // ANIMAÇÕES
    // ========================================

    // Fade in em cards ao carregar
    $('.card').each(function(index) {
        $(this).css({
            'animation-delay': `${index * 0.1}s`
        }).addClass('fade-in');
    });

    // ========================================
    // NOTIFICAÇÕES
    // ========================================

    // Função para mostrar notificação
    window.showNotification = function(message, type = 'info') {
        const colors = {
            success: '#28a745',
            error: '#dc3545',
            warning: '#ffc107',
            info: '#17a2b8'
        };

        const notification = $(`
            <div class="alert alert-${type} alert-dismissible fade show position-fixed"
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;"
                 role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);

        $('body').append(notification);

        setTimeout(function() {
            notification.fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
    };

    // ========================================
    // LOADING OVERLAY
    // ========================================

    window.showLoading = function() {
        if (!$('.spinner-overlay').length) {
            $('body').append(`
                <div class="spinner-overlay">
                    <div class="spinner"></div>
                </div>
            `);
        }
        $('.spinner-overlay').addClass('active');
    };

    window.hideLoading = function() {
        $('.spinner-overlay').removeClass('active');
    };

    // Mostrar loading ao enviar formulários
    $('form').on('submit', function() {
        showLoading();
    });

    // ========================================
    // UTILITÁRIOS
    // ========================================

    // Formatar data e hora
    window.formatDateTime = function(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('pt-BR');
    };

    // Formatar moeda
    window.formatCurrency = function(value) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(value);
    };

    // Debounce para busca
    window.debounce = function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    };

    // ========================================
    // CONSOLE LOG (apenas em desenvolvimento)
    // ========================================

    console.log('%c Sistema de Chamados - Sabores Refeitório ',
                'background: linear-gradient(135deg, #FF6B35 0%, #004E89 100%); color: white; padding: 10px; font-size: 16px; font-weight: bold;');
    console.log('%c Desenvolvido com CodeIgniter 4 + Bootstrap 5 ',
                'color: #6c757d; font-size: 12px;');
});
