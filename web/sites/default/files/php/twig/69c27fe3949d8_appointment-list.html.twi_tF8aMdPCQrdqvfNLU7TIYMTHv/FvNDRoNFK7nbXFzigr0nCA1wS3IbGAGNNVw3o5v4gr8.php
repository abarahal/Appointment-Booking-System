<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* modules/custom/appointment/templates/appointment-list.html.twig */
class __TwigTemplate_fda8d1b637bbdefb7d91661efd7d5c78 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->extensions[SandboxExtension::class];
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 9
        yield "<div class=\"appointment-list\">
  <h2>";
        // line 10
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Mes rendez-vous"));
        yield "</h2>

  ";
        // line 12
        if (Twig\Extension\CoreExtension::testEmpty(($context["rows"] ?? null))) {
            // line 13
            yield "    <div class=\"appointment-list__empty\">
      <p>";
            // line 14
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Aucun rendez-vous trouvé."));
            yield "</p>
      <a href=\"";
            // line 15
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar($this->extensions['Drupal\Core\Template\TwigExtension']->getPath("appointment.book"));
            yield "\" class=\"button button--primary\">";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Prendre un rendez-vous"));
            yield "</a>
      <a href=\"";
            // line 16
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["lookup_url"] ?? null), "html", null, true);
            yield "\" class=\"button\">";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Rechercher par email ou téléphone"));
            yield "</a>
    </div>
  ";
        } else {
            // line 19
            yield "    <div class=\"appointment-list__actions\">
      <a href=\"";
            // line 20
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar($this->extensions['Drupal\Core\Template\TwigExtension']->getPath("appointment.book"));
            yield "\" class=\"button button--primary\">";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Nouveau rendez-vous"));
            yield "</a>
      <a href=\"";
            // line 21
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["lookup_url"] ?? null), "html", null, true);
            yield "\" class=\"button\">";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Nouvelle recherche"));
            yield "</a>
    </div>

    <table class=\"appointment-list__table\">
      <thead>
        <tr>
          <th>";
            // line 27
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("#"));
            yield "</th>
          <th>";
            // line 28
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Agence"));
            yield "</th>
          <th>";
            // line 29
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Conseiller"));
            yield "</th>
          <th>";
            // line 30
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Type"));
            yield "</th>
          <th>";
            // line 31
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Date et heure"));
            yield "</th>
          <th>";
            // line 32
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Statut"));
            yield "</th>
          <th>";
            // line 33
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Actions"));
            yield "</th>
        </tr>
      </thead>
      <tbody>
        ";
            // line 37
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(($context["rows"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["row"]) {
                // line 38
                yield "          <tr class=\"appointment-row appointment-row--";
                yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["row"], "status", [], "any", false, false, true, 38), "html", null, true);
                yield "\">
            <td>";
                // line 39
                yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["row"], "id", [], "any", false, false, true, 39), "html", null, true);
                yield "</td>
            <td>";
                // line 40
                yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["row"], "agency", [], "any", false, false, true, 40), "html", null, true);
                yield "</td>
            <td>";
                // line 41
                yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["row"], "adviser", [], "any", false, false, true, 41), "html", null, true);
                yield "</td>
            <td>";
                // line 42
                yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["row"], "type", [], "any", false, false, true, 42), "html", null, true);
                yield "</td>
            <td>";
                // line 43
                yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["row"], "start_time", [], "any", false, false, true, 43), "html", null, true);
                yield "</td>
            <td>
              <span class=\"appointment-status appointment-status--";
                // line 45
                yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["row"], "status", [], "any", false, false, true, 45), "html", null, true);
                yield "\">
                ";
                // line 46
                yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, Twig\Extension\CoreExtension::capitalize($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["row"], "status", [], "any", false, false, true, 46)), "html", null, true);
                yield "
              </span>
            </td>
            <td class=\"appointment-actions\">
              <a href=\"";
                // line 50
                yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["row"], "edit_url", [], "any", false, false, true, 50), "html", null, true);
                yield "\" class=\"button button--small\">";
                yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Modifier"));
                yield "</a>
              ";
                // line 51
                if ((CoreExtension::getAttribute($this->env, $this->source, $context["row"], "status", [], "any", false, false, true, 51) != "cancelled")) {
                    // line 52
                    yield "                <a href=\"";
                    yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["row"], "cancel_url", [], "any", false, false, true, 52), "html", null, true);
                    yield "\" class=\"button button--small button--danger\">";
                    yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Annuler"));
                    yield "</a>
              ";
                }
                // line 54
                yield "            </td>
          </tr>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['row'], $context['_parent']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 57
            yield "      </tbody>
    </table>
  ";
        }
        // line 60
        yield "</div>
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["rows", "lookup_url"]);        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "modules/custom/appointment/templates/appointment-list.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  196 => 60,  191 => 57,  183 => 54,  175 => 52,  173 => 51,  167 => 50,  160 => 46,  156 => 45,  151 => 43,  147 => 42,  143 => 41,  139 => 40,  135 => 39,  130 => 38,  126 => 37,  119 => 33,  115 => 32,  111 => 31,  107 => 30,  103 => 29,  99 => 28,  95 => 27,  84 => 21,  78 => 20,  75 => 19,  67 => 16,  61 => 15,  57 => 14,  54 => 13,  52 => 12,  47 => 10,  44 => 9,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "modules/custom/appointment/templates/appointment-list.html.twig", "/Users/void/schedulehub_project/web/modules/custom/appointment/templates/appointment-list.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = ["if" => 12, "for" => 37];
        static $filters = ["t" => 10, "escape" => 16, "capitalize" => 46];
        static $functions = ["path" => 15];

        try {
            $this->sandbox->checkSecurity(
                ['if', 'for'],
                ['t', 'escape', 'capitalize'],
                ['path'],
                $this->source
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
