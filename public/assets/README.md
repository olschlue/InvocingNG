# Assets für InvoicingNG

## PDF-Hintergrundbild

Um ein Hintergrundbild für Ihre Rechnungen zu verwenden:

1. **Legen Sie Ihr PNG-Bild hier ab** als `invoice_background.png`
2. **Empfohlene Spezifikationen:**
   - Format: PNG
   - Größe: 2480 x 3508 Pixel (A4 bei 300 DPI)
   - Oder: 1754 x 2480 Pixel (A4 bei 210 DPI)
   - Minimale Auflösung: 595 x 842 Pixel (A4 bei 72 DPI)
   - Dateiname: `invoice_background.png`

3. **Der Pfad ist konfiguriert in:**
   - `config/config.php` → `PDF_BACKGROUND`
   - Standard: `/public/assets/invoice_background.png`

4. **Hinweise:**
   - Das Bild wird vollflächig (0,0 bis 210mm x 297mm) eingefügt
   - Es erscheint auf jeder Seite der PDF-Rechnung
   - Achten Sie auf helle/durchsichtige Bereiche, damit der Text lesbar bleibt
   - Logo und andere Inhalte werden über dem Hintergrundbild platziert

## Beispiel-Hintergrundbild erstellen

Sie können mit einem Grafikprogramm (z.B. GIMP, Photoshop, Canva) ein Hintergrundbild erstellen:

- Verwenden Sie dezente Farben oder Wasserzeichen
- Fügen Sie ggf. ein Logo als Wasserzeichen hinzu
- Achten Sie darauf, dass der Hauptbereich für Text frei bleibt
- Speichern Sie als PNG mit Transparenz wenn gewünscht
