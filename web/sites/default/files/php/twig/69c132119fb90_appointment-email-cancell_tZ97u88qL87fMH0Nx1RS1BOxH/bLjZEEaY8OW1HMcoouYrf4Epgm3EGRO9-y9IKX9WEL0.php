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

/* modules/custom/appointment/templates/email/appointment-email-cancellation.html.twig */
class __TwigTemplate_c3723cd05458fba76d38e61328d0cd05 extends Template
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
        // line 10
        yield "<div style=\"font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;\">
  <div style=\"background: #dc3545; color: #fff; padding: 20px; border-radius: 6px 6px 0 0; text-align: center;\">
    <h1 style=\"margin: 0; font-size: 22px;\">&#10007; ";
        // line 12
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Rendez-vous annulé"));
        yield "</h1>
  </div>

  <div style=\"border: 1px solid #ddd; border-top: none; padding: 20px; border-radius: 0 0 6px 6px;\">
    <p>";
        // line 16
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Bonjour"));
        yield " <strong>";
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["appointment"] ?? null), "client_name", [], "any", false, false, true, 16), "value", [], "any", false, false, true, 16), "html", null, true);
        yield "</strong>,</p>

    <p>";
        // line 18
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Votre rendez-vous a bien été annulé."));
        yield "</p>

    <table style=\"width: 100%; border-collapse: collapse; margin: 15px 0;\">
      <tr>
        <td style=\"padding: 8px; border-bottom: 1px solid #eee; color: #666; width: 40%;\">";
        // line 22
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Référence"));
        yield "</td>
        <td style=\"padding: 8px; border-bottom: 1px solid #eee;\"><strong>#";
        // line 23
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["appointment"] ?? null), "id", [], "any", false, false, true, 23), "value", [], "any", false, false, true, 23), "html", null, true);
        yield "</strong></td>
      </tr>
      <tr>
        <td style=\"padding: 8px; border-bottom: 1px solid #eee; color: #666;\">";
        // line 26
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Agence"));
        yield "</td>
        <td style=\"padding: 8px; border-bottom: 1px solid #eee;\">";
        // line 27
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["appointment"] ?? null), "agency", [], "any", false, false, true, 27), "entity", [], "any", false, false, true, 27), "label", [], "any", false, false, true, 27), "html", null, true);
        yield "</td>
      </tr>
      <tr>
        <td style=\"padding: 8px; border-bottom: 1px solid #eee; color: #666;\">";
        // line 30
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Conseiller"));
        yield "</td>
        <td style=\"padding: 8px; border-bottom: 1px solid #eee;\">";
        // line 31
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["appointment"] ?? null), "adviser_name", [], "any", false, false, true, 31), "value", [], "any", false, false, true, 31), "html", null, true);
        yield "</td>
      </tr>
      <tr>
        <td style=\"padding: 8px; border-bottom: 1px solid #eee; color: #666;\">";
        // line 34
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Date prévue"));
        yield "</td>
        <td style=\"padding: 8px; border-bottom: 1px solid #eee; text-decoration: line-through;\">";
        // line 35
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["date_label"] ?? null), "html", null, true);
        yield "</td>
      </tr>
      <tr>
        <td style=\"padding: 8px; border-bottom: 1px solid #eee; color: #666;\">";
        // line 38
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Statut"));
        yield "</td>
        <td style=\"padding: 8px; border-bottom: 1px solid #eee; color: #dc3545; font-weight: bold;\">";
        // line 39
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Annulé"));
        yield "</td>
      </tr>
    </table>

    <p style=\"text-align: center; margin: 25px 0;\">
      <a href=\"";
        // line 44
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["manage_url"] ?? null), "html", null, true);
        yield "\" style=\"background: #0074bd; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 4px; display: inline-block;\">
        ";
        // line 45
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Reprendre un nouveau rendez-vous"));
        yield "
      </a>
    </p>

    <p style=\"color: #999; font-size: 12px; margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px;\">
      ";
        // line 50
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Ce message a été envoyé automatiquement par"));
        yield " ";
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["site_name"] ?? null), "html", null, true);
        yield ".
    </p>
  </div>
</div>
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["appointment", "date_label", "manage_url", "site_name"]);        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "modules/custom/appointment/templates/email/appointment-email-cancellation.html.twig";
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
        return array (  133 => 50,  125 => 45,  121 => 44,  113 => 39,  109 => 38,  103 => 35,  99 => 34,  93 => 31,  89 => 30,  83 => 27,  79 => 26,  73 => 23,  69 => 22,  62 => 18,  55 => 16,  48 => 12,  44 => 10,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "modules/custom/appointment/templates/email/appointment-email-cancellation.html.twig", "/Users/void/schedulehub_project/web/modules/custom/appointment/templates/email/appointment-email-cancellation.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = [];
        static $filters = ["t" => 12, "escape" => 16];
        static $functions = [];

        try {
            $this->sandbox->checkSecurity(
                [],
                ['t', 'escape'],
                [],
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
