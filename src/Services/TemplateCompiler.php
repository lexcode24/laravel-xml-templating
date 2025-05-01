<?php

namespace Lexcode\XmlTemplating\Services;

use DOMDocument;
use DOMXPath;
use Lexcode\XmlTemplating\Models\Template;
use Lexcode\XmlTemplating\Models\TemplateModification;
use Illuminate\Support\Facades\File;

class TemplateCompiler
{
    /**
     * Kompiliert ein Template und wendet alle Modifikationen an.
     *
     * @param string $templateName
     * @param string|null $moduleName
     * @return string Der Pfad zur kompilierten Blade-Datei
     * @throws \Exception
     */
    public function compile(string $templateName, ?string $moduleName = null): string
    {
        // Template aus der Datenbank laden
        $template = Template::where('name', $templateName)
            ->where('active', true)
            ->when($moduleName, function ($query) use ($moduleName) {
                return $query->where('module_name', $moduleName);
            })
            ->first();
        
        if (!$template) {
            throw new \Exception("Template nicht gefunden: {$templateName}");
        }
        
        // XML-Inhalt laden
        $xmlContent = $template->xml_content;
        $dom = new DOMDocument();
        $dom->loadXML($xmlContent, LIBXML_NOERROR);
        
        // Vererbung verarbeiten
        if ($template->parent_template_id) {
            $xmlContent = $this->applyInheritance($dom, $template->parent_template_id);
            $dom->loadXML($xmlContent, LIBXML_NOERROR);
        }
        
        // Modifikationen anwenden
        $modifications = TemplateModification::where('template_id', $template->id)
            ->orderBy('priority')
            ->get();
        
        foreach ($modifications as $modification) {
            $this->applyModification($dom, $modification);
        }
        
        // XML in Blade-Template konvertieren
        $bladeContent = $this->convertToBlade($dom);
        
        // Verzeichnis erstellen, falls es nicht existiert
        $directory = storage_path('framework/views/xml-templates');
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
        
        // Blade-Datei speichern
        $bladePath = "{$directory}/{$templateName}.blade.php";
        File::put($bladePath, $bladeContent);
        
        return $bladePath;
    }
    
    /**
     * Wendet die Vererbung auf ein Template an.
     *
     * @param DOMDocument $dom
     * @param int $parentTemplateId
     * @return string
     * @throws \Exception
     */
    protected function applyInheritance(DOMDocument $dom, int $parentTemplateId): string
    {
        $parentTemplate = Template::find($parentTemplateId);
        
        if (!$parentTemplate) {
            throw new \Exception("Übergeordnetes Template nicht gefunden: ID {$parentTemplateId}");
        }
        
        $parentDom = new DOMDocument();
        $parentDom->loadXML($parentTemplate->xml_content, LIBXML_NOERROR);
        
        // Hier würde die Logik für die Vererbung implementiert
        // Zum Beispiel: Elemente aus dem Kind-Template in das Eltern-Template einfügen
        
        return $parentDom->saveXML();
    }
    
    /**
     * Wendet eine Modifikation auf das DOM an.
     *
     * @param DOMDocument $dom
     * @param TemplateModification $modification
     * @return void
     */
    protected function applyModification(DOMDocument $dom, TemplateModification $modification): void
    {
        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query($modification->xpath);
        
        if ($nodes && $nodes->length > 0) {
            foreach ($nodes as $node) {
                $fragment = $dom->createDocumentFragment();
                $fragment->appendXML($modification->content);
                
                switch ($modification->operation) {
                    case 'replace':
                        $node->parentNode->replaceChild($fragment, $node);
                        break;
                    case 'inside':
                        $node->appendChild($fragment);
                        break;
                    case 'before':
                        $node->parentNode->insertBefore($fragment, $node);
                        break;
                    case 'after':
                        if ($node->nextSibling) {
                            $node->parentNode->insertBefore($fragment, $node->nextSibling);
                        } else {
                            $node->parentNode->appendChild($fragment);
                        }
                        break;
                }
            }
        }
    }
    
    /**
     * Konvertiert XML in Blade-Syntax.
     *
     * @param DOMDocument $dom
     * @return string
     */
    protected function convertToBlade(DOMDocument $dom): string
    {
        // XML in String umwandeln
        $xml = $dom->saveXML();
        
        // Spezielle Tags in Blade-Direktiven umwandeln
        $blade = preg_replace('/<t-if condition="([^"]+)">/', '@if($1)', $xml);
        $blade = preg_replace('/<\/t-if>/', '@endif', $blade);
        $blade = preg_replace('/<t-foreach items="([^"]+)" as="([^"]+)">/', '@foreach($1 as $2)', $blade);
        $blade = preg_replace('/<\/t-foreach>/', '@endforeach', $blade);
        
        // Variablen ersetzen
        $blade = preg_replace('/\${([^}]+)}/', '{{ $1 }}', $blade);
        
        return $blade;
    }
}