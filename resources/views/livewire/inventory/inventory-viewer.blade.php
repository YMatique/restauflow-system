
<div class="space-y-6">
    <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl shadow-sm border">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">


        <div class="flex flex-col">
            <h1 class="text-2xl font-bold">Inventário</h1>
             <!-- Data de criação -->
            <div class="text-sm text-zinc-600 dark:text-zinc-400">
                Criado em: {{ $inventory->created_at->format('d/m/Y H:i') }}
            </div>
        </div>

        <div class="flex flex-col md:flex-row items-start md:items-center gap-3">


            <!-- Botão Imprimir -->
            <button type="button"
                    onclick="printSection('printable', 'Inventário', '{{ auth()->user()->name }}')"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                Imprimir
            </button>

            <!-- Botão Exportar Excel -->
            <button type="button"
                    onclick="exportToExcel('printable')"
                    {{-- wire:click="exportExcel" --}}
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors">
                Exportar Excel
            </button>

            <!-- Botão Exportar PDF -->
            <button type="button"
                    onclick="exportToPDF('printable', 'Inventário', '{{ auth()->user()->name }}')"
                    {{-- wire:click="exportPDF" --}}
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors">
                Exportar PDF
            </button>
        </div>
    </div>
</div>



    <!-- Table Section -->
    <div id="printable" class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-zinc-50 dark:bg-zinc-900/70 border-b border-zinc-200 dark:border-zinc-700">
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider whitespace-nowrap">#</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider whitespace-nowrap">Name</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider whitespace-nowrap">Details</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider whitespace-nowrap">Status</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider whitespace-nowrap">Quantity</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider whitespace-nowrap">Price</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider whitespace-nowrap">Subtotal</th>
                </tr>
            </thead>

            <tbody class="bg-white dark:bg-zinc-800">
                @forelse($inventory->items as $item)
                    <tr class="border-b">
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">{{ $item->product->name }}</td>
                        <td class="px-4 py-3">{{ $item->product->category->name ?? '--' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-sm rounded bg-zinc-200">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $item->quantity }}</td>
                        <td class="px-4 py-3">{{ $item->price }}</td>
                        <td class="px-4 py-3">{{ $item->subtotal }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-zinc-500">
                            No records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            <tfoot>
                <tr class="bg-zinc-50 dark:bg-zinc-900/70 font-semibold">
                    <td colspan="6" class="px-6 py-4 text-right">Subtotal</td>
                    <td class="px-6 py-4">{{ number_format($inventory->subtotal, 2) }}</td>
                </tr>
                <tr class="bg-zinc-50 dark:bg-zinc-900/70 font-semibold">
                    <td colspan="6" class="px-6 py-4 text-right">IVA (17%)</td>
                    <td class="px-6 py-4">{{ number_format($inventory->iva, 2) }}</td>
                </tr>
                <tr class="bg-zinc-100 dark:bg-zinc-900 font-bold">
                    <td colspan="6" class="px-6 py-4 text-right">Total (MZN)</td>
                    <td class="px-6 py-4">{{ number_format($inventory->total, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>


<script>
    function printSection(sectionId, docName = 'Inventário', userName = 'Usuário') {
        const section = document.getElementById(sectionId);
        if (!section) {
            console.error('Seção não encontrada:', sectionId);
            return;
        }

        const clone = section.cloneNode(true);

        const creationDate = clone.dataset.creationDate || new Date().toLocaleDateString();
        const now = new Date();
        const printDate = `${now.toLocaleDateString()} ${now.toLocaleTimeString()}`;

        const printWindow = window.open('', '_blank');
        if (!printWindow) {
            console.error('Não foi possível abrir a janela de impressão');
            return;
        }

        const header = `
            <div class="print-header">
                <h1 style="margin-bottom: 5px;">${docName}</h1>
                <p style="margin: 2px 0;">Data de criação: ${creationDate}</p>
                <p style="margin: 2px 0;">Data de impressão: ${printDate}</p>
                <hr style="margin: 10px 0;">
            </div>
        `;

        const style = `
            <style>
                body { margin: 10mm 10mm; font-family: Arial, sans-serif; }

                /* 🔥 Expande a tabela totalmente */
                table {
                    width: 100% !important;
                    border-collapse: collapse;
                    table-layout: fixed;
                }

                th, td {
                    border: 1px solid #000;
                    padding: 8px;
                    width: auto !important;   /* impede encolhimento */
                    word-wrap: break-word;
                }

                th { background-color: #f0f0f0; }
                td.numeric { text-align: right; }

                thead { display: table-header-group; }
                tfoot { display: table-footer-group; }

                /* Rodapé elegante */
                tfoot td {
                    font-weight: bold;
                    border-top: 2px solid #000;
                    border-bottom: none;
                    text-align: right;
                }

                tfoot tr:last-child td {
                    border-bottom: 2px solid #000;
                }

                .print-header { text-align: center; margin-bottom: 10px; }

                @media print {
                    @page { size: A4 portrait; margin: 10mm 10mm; }

                    body::before {
                        content: "Impresso por: ${userName}";
                        position: fixed;
                        bottom: 0;
                        left: 0;
                        font-size: 12px;
                        color: #333;
                    }

                    body::after {
                        content: "Página " counter(page);
                        position: fixed;
                        bottom: 0;
                        right: 0;
                        font-size: 12px;
                        color: #333;
                    }
                }
            </style>
        `;

        // Alinhamento das colunas numéricas do corpo
        clone.querySelectorAll('tbody tr').forEach(row => {
            const tds = row.querySelectorAll('td');
            if (tds.length >= 7) {
                tds[4].classList.add('numeric');
                tds[5].classList.add('numeric');
                tds[6].classList.add('numeric');
            }
        });

        // Alinhamento do tfoot
        clone.querySelectorAll('tfoot tr').forEach(row => {
            const tds = row.querySelectorAll('td');
            if (tds.length >= 2) {
                tds[tds.length - 1].classList.add('numeric');
            }
        });

        printWindow.document.write(`
            <html>
                <head>
                    <title>${docName}</title>
                    ${style}
                </head>
                <body>
                    ${header}
                    ${clone.innerHTML}
                </body>
            </html>
        `);

        printWindow.document.close();
        printWindow.focus();

        printWindow.print();
        printWindow.close();
    }

    function exportToExcel(sectionId, fileName = null) {
        const section = document.getElementById(sectionId);
        if (!section) {
            console.error('Seção não encontrada:', sectionId);
            return;
        }

        const table = section.querySelector('table');
        if (!table) {
            console.error('Nenhuma tabela encontrada na seção');
            return;
        }

        // Gera timestamp seguro para nome de arquivo
        const now = new Date();
        const date =
            now.getFullYear() + '-' +
            String(now.getMonth() + 1).padStart(2, '0') + '-' +
            String(now.getDate()).padStart(2, '0');

        const time =
            String(now.getHours()).padStart(2, '0') + '-' +
            String(now.getMinutes()).padStart(2, '0') + '-' +
            String(now.getSeconds()).padStart(2, '0');

        // Se o usuário não passar fileName → gera automaticamente
        if (!fileName) {
            fileName = `inventario_${date}_${time}.xlsx`;
        }

        // Converte a tabela HTML para XLSX
        const workbook = XLSX.utils.table_to_book(table, { sheet: "Inventário" });

        // Faz download
        XLSX.writeFile(workbook, fileName);
    }

    async function exportToPDF(sectionId, docName = 'Inventário', userName = 'Usuário') {
        const section = document.getElementById(sectionId);
        if (!section) {
            console.error('Seção não encontrada:', sectionId);
            return;
        }

        // Clona a seção
        const clone = section.cloneNode(true);

        // Datas
        const creationDate = clone.dataset.creationDate || new Date().toLocaleDateString();
        const now = new Date();
        const exportDate = `${now.toLocaleDateString()} ${now.toLocaleTimeString()}`;

        // Envolve tudo num container separado
        const wrapper = document.createElement("div");
        wrapper.style.padding = "20px"; // padding interno do wrapper
        wrapper.style.backgroundColor = "#fff";
        wrapper.style.color = "#000";
        wrapper.style.fontFamily = "Arial, sans-serif";
        wrapper.style.fontSize = "12px"; // força tamanho da fonte
        wrapper.innerHTML = `
            <style>
                /* Força todas cores para preto e branco e fonte 12px */
                * {
                    color: #000 !important;
                    background-color: #fff !important;
                    border-color: #000 !important;
                    font-size: 12px !important;
                }
                *::before, *::after {
                    color: #000 !important;
                    background-color: #fff !important;
                    border-color: #000 !important;
                    font-size: 12px !important;
                }

                body, div { font-family: Arial, sans-serif; font-size: 12px; color: #000; background: #fff; }

                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #000; padding: 6px; text-align: left; }
                th { background: #f5f5f5; font-weight: bold; }
                td.numeric { text-align: right; font-weight: bold; }
                tfoot td { border-top: 2px solid #000; font-weight: bold; }
            </style>

            <h1 style="text-align:center; margin:0 0 5px 0; font-size:12px;">${docName}</h1>
            <p style="text-align:center; margin:2px 0; font-size:12px;">Data de criação: ${creationDate}</p>
            <p style="text-align:center; margin:2px 0; font-size:12px;">Exportado em: ${exportDate}</p>
            <hr style="margin:10px 0; border:1px solid #000;">
            ${clone.innerHTML}
        `;

        document.body.appendChild(wrapper);

        // Força cores e tamanho da fonte no wrapper e todos filhos
        wrapper.querySelectorAll("*").forEach(el => {
            el.style.color = "#000";
            el.style.backgroundColor = "#fff";
            el.style.borderColor = "#000";
            el.style.fontSize = "12px";
        });

        // Aguarda renderização
        await new Promise(r => setTimeout(r, 300));

        try {
            const canvas = await html2canvas(wrapper, {
                scale: 2,
                useCORS: true,
                backgroundColor: "#fff"
            });

            const imgData = canvas.toDataURL("image/png");

            // PDF A4 retrato com margens
            const pdf = new jspdf.jsPDF("p", "mm", "a4");
            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();
            const margin = 15; // margens em mm

            const imgProps = pdf.getImageProperties(imgData);
            const pdfWidth = pageWidth - 2 * margin;
            const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

            let position = margin;

            // Se a altura da imagem for maior que a página, divide em múltiplas páginas
            if (pdfHeight <= pageHeight - 2 * margin) {
                pdf.addImage(imgData, "PNG", margin, position, pdfWidth, pdfHeight);
            } else {
                let remainingHeight = imgProps.height;
                let canvasPageHeight = ((pageHeight - 2 * margin) * imgProps.width) / pdfWidth;
                let yOffset = 0;

                while (remainingHeight > 0) {
                    const pageCanvas = document.createElement("canvas");
                    pageCanvas.width = canvas.width;
                    pageCanvas.height = Math.min(canvasPageHeight, remainingHeight);

                    const pageCtx = pageCanvas.getContext("2d");
                    pageCtx.drawImage(canvas, 0, yOffset, canvas.width, pageCanvas.height, 0, 0, canvas.width, pageCanvas.height);

                    const pageData = pageCanvas.toDataURL("image/png");
                    pdf.addImage(pageData, "PNG", margin, position, pdfWidth, (pageCanvas.height * pdfWidth) / canvas.width);

                    remainingHeight -= pageCanvas.height;
                    yOffset += pageCanvas.height;

                    if (remainingHeight > 0) pdf.addPage();
                }
            }

            pdf.save(`${docName}.pdf`);
        } catch (error) {
            console.error("Erro ao gerar PDF:", error);
        } finally {
            wrapper.remove();
        }
    }

</script>

{{-- Importação para CXV --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

{{-- IMportacao para PDF --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
