<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EmailTemplateController extends Controller
{
    protected $emailTemplate;

    public function __construct(?EmailTemplate $emailTemplate = null)
    {
        $this->emailTemplate = $emailTemplate;
    }

    /**
     * LISTAR – Exibe todos os modelos de e-mail
     */
    public function index()
    {
        $emailtemplates = EmailTemplate::orderBy('id', 'desc')->get();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Email Templates retrieved successfully.',
                'data' => $emailTemplates,
            ]);
        }

        $breadcrumbs = [
            ['label' => 'Email Templates', 'url' => null],
        ];

        $nav_bar = 'emailtemplate';
        $pageTitle = 'Email Templates';

        return view('admin.emailtemplate.list', compact('emailtemplates', 'pageTitle', 'nav_bar', 'breadcrumbs'));
    }

    /**
     * CREATE – Exibe o formulário de criação
     */
    public function create()
    {
        $pageTitle = "Add Email Template";
        $nav_bar = "emailtemplate";
        $breadcrumbs = [
            ['label' => 'Email Templates', 'url' => route('emailtemplate.index')],
            ['label' => 'Add Email Template', 'url' => null],
        ];

        return view('admin.emailtemplate.form', [
            'action' => 'create',
            'pageTitle' => $pageTitle,
            'nav_bar' => $nav_bar,
            'breadcrumbs' => $breadcrumbs,
            'emailTemplate' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * STORE – Salva um novo modelo de e-mail no banco
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'status' => 'nullable|boolean',
            'description' => 'nullable|string',
            'created_at' => 'nullable|date',
        ]);

        EmailTemplate::create($validated);

        return redirect()->route('emailtemplate.index')
            ->with('success', 'Email Template created successfully.');
    }

    /**
     * EDIT – Exibe o formulário de edição
     */
    public function edit($id)
    {
        $emailTemplate = EmailTemplate::findOrFail($id);

        $pageTitle = "Edit Email Template";
        $nav_bar = "emailtemplate";
        $breadcrumbs = [
            ['label' => 'Email Templates', 'url' => route('emailtemplate.index')],
            ['label' => 'Edit Email Template', 'url' => null],
        ];

        return view('admin.emailtemplate.form', [
            'action' => 'edit',
            'pageTitle' => $pageTitle,
            'nav_bar' => $nav_bar,
            'breadcrumbs' => $breadcrumbs,
            'emailTemplate' => $emailTemplate,
        ]);
    }

    /**
     * UPDATE – Atualiza um modelo de e-mail existente
     */
    public function update(Request $request, $id)
    {
        $emailTemplate = EmailTemplate::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'status' => 'nullable|boolean',
            'description' => 'nullable|string',
            'created_at' => 'nullable|date',
        ]);

        $emailTemplate->update([
            'name' => $request->name,
            'subject' => $request->subject,
            'body' => $request->body,
            'status' => $request->status,
            'description' => $request->description,
            'created_at' => $request->created_at,
        ]);

        return redirect()->route('emailtemplate.index')
            ->with('success', 'Email Template updated successfully.');
    }

    /**
     * DELETE – Remove um modelo de e-mail
     */
    public function destroy($id)
    {
        $emailTemplate = EmailTemplate::find($id);

        if (!$emailTemplate) {
            return redirect()->route('emailtemplate.index')
                ->with('error', 'Email Template not found.');
        }

        $emailTemplate->delete();

        return redirect()->route('emailtemplate.index')
            ->with('success', 'Email Template deleted successfully.');
    }

    public function activate($id)
    {
        $template = EmailTemplate::findOrFail($id);
        $template->status = 1; // ativo
        $template->save();

        return redirect()->route('emailtemplate.index')
                        ->with('success', 'Email Template activated successfully.');
    }

    public function deactivate($id)
    {
        $template = EmailTemplate::findOrFail($id);
        $template->status = 0; // desativado
        $template->save();

        return redirect()->route('emailtemplate.index')
                        ->with('success', 'Email Template deactivated successfully.');
    }

}
