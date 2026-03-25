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

/* modules/custom/appointment/templates/appointment-confirmation.html.twig */
class __TwigTemplate_f8cf418dd4f3298f8d5b8439e8be40d0 extends Template
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
        yield "<div class=\"appointment-confirmation\">
  <div class=\"appointment-confirmation__icon\">&#10003;</div>
  <h2>";
        // line 11
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Votre rendez-vous est confirmé !"));
        yield "</h2>

  <div class=\"appointment-confirmation__details\">
    <table>
      <tr>
        <th>";
        // line 16
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Référence"));
        yield "</th>
        <td>#";
        // line 17
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["appointment"] ?? null), "id", [], "any", false, false, true, 17), "value", [], "any", false, false, true, 17), "html", null, true);
        yield "</td>
      </tr>
      <tr>
        <th>";
        // line 20
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Client"));
        yield "</th>
        <td>";
        // line 21
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["appointment"] ?? null), "client_name", [], "any", false, false, true, 21), "value", [], "any", false, false, true, 21), "html", null, true);
        yield "</td>
      </tr>
      <tr>
        <th>";
        // line 24
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Email"));
        yield "</th>
        <td>";
        // line 25
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["appointment"] ?? null), "client_email", [], "any", false, false, true, 25), "value", [], "any", false, false, true, 25), "html", null, true);
        yield "</td>
      </tr>
      ";
        // line 27
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["appointment"] ?? null), "client_phone", [], "any", false, false, true, 27), "value", [], "any", false, false, true, 27)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 28
            yield "      <tr>
        <th>";
            // line 29
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Téléphone"));
            yield "</th>
        <td>";
            // line 30
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["appointment"] ?? null), "client_phone", [], "any", false, false, true, 30), "value", [], "any", false, false, true, 30), "html", null, true);
            yield "</td>
      </tr>
      ";
        }
        // line 33
        yield "      <tr>
        <th>";
        // line 34
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Agence"));
        yield "</th>
        <td>";
        // line 35
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["appointment"] ?? null), "agency", [], "any", false, false, true, 35), "entity", [], "any", false, false, true, 35), "label", [], "any", false, false, true, 35), "html", null, true);
        yield "</td>
      </tr>
      <tr>
        <th>";
        // line 38
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Conseiller"));
        yield "</th>
        <td>";
        // line 39
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["appointment"] ?? null), "adviser_name", [], "any", false, false, true, 39), "value", [], "any", false, false, true, 39), "html", null, true);
        yield "</td>
      </tr>
      <tr>
        <th>";
        // line 42
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Type"));
        yield "</th>
        <td>";
        // line 43
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["appointment"] ?? null), "appointment_type", [], "any", false, false, true, 43), "entity", [], "any", false, false, true, 43), "label", [], "any", false, false, true, 43), "html", null, true);
        yield "</td>
      </tr>
      <tr>
        <th>";
        // line 46
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Date et heure"));
        yield "</th>
        <td>";
        // line 47
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["appointment"] ?? null), "start_time", [], "any", false, false, true, 47), "value", [], "any", false, false, true, 47), "d/m/Y H:i"), "html", null, true);
        yield "</td>
      </tr>
      <tr>
        <th>";
        // line 50
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Statut"));
        yield "</th>
        <td>
          <span class=\"appointment-status appointment-status--";
        // line 52
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["appointment"] ?? null), "status", [], "any", false, false, true, 52), "value", [], "any", false, false, true, 52), "html", null, true);
        yield "\">
            ";
        // line 53
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, Twig\Extension\CoreExtension::capitalize($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["appointment"] ?? null), "status", [], "any", false, false, true, 53), "value", [], "any", false, false, true, 53)), "html", null, true);
        yield "
          </span>
        </td>
      </tr>
      ";
        // line 57
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["appointment"] ?? null), "notes", [], "any", false, false, true, 57), "value", [], "any", false, false, true, 57)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 58
            yield "      <tr>
        <th>";
            // line 59
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Notes"));
            yield "</th>
        <td>";
            // line 60
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["appointment"] ?? null), "notes", [], "any", false, false, true, 60), "value", [], "any", false, false, true, 60), "html", null, true);
            yield "</td>
      </tr>
      ";
        }
        // line 63
        yield "    </table>
  </div>

  <div class=\"appointment-confirmation__actions\">
    <a href=\"";
        // line 67
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["edit_url"] ?? null), "html", null, true);
        yield "\" class=\"button button--primary\">";
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Modifier le rendez-vous"));
        yield "</a>
    <a href=\"";
        // line 68
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["cancel_url"] ?? null), "html", null, true);
        yield "\" class=\"button button--danger\">";
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Annuler le rendez-vous"));
        yield "</a>
    <a href=\"";
        // line 69
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar($this->extensions['Drupal\Core\Template\TwigExtension']->getPath("appointment.lookup"));
        yield "\" class=\"button\">";
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Rechercher un rendez-vous"));
        yield "</a>
  </div>
</div>
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["appointment", "edit_url", "cancel_url"]);        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "modules/custom/appointment/templates/appointment-confirmation.html.twig";
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
        return array (  192 => 69,  186 => 68,  180 => 67,  174 => 63,  168 => 60,  164 => 59,  161 => 58,  159 => 57,  152 => 53,  148 => 52,  143 => 50,  137 => 47,  133 => 46,  127 => 43,  123 => 42,  117 => 39,  113 => 38,  107 => 35,  103 => 34,  100 => 33,  94 => 30,  90 => 29,  87 => 28,  85 => 27,  80 => 25,  76 => 24,  70 => 21,  66 => 20,  60 => 17,  56 => 16,  48 => 11,  44 => 9,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "modules/custom/appointment/templates/appointment-confirmation.html.twig", "/Users/void/schedulehub_project/web/modules/custom/appointment/templates/appointment-confirmation.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = ["if" => 27];
        static $filters = ["t" => 11, "escape" => 17, "date" => 47, "capitalize" => 53];
        static $functions = ["path" => 69];

        try {
            $this->sandbox->checkSecurity(
                ['if'],
                ['t', 'escape', 'date', 'capitalize'],
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
