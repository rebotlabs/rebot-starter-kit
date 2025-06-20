import InputError from "@/components/input-error"
import { Button } from "@/components/ui/button"
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from "@/components/ui/dialog"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { useTranslation } from "@/hooks/use-i18n"
import { useForm } from "@inertiajs/react"
import { LoaderCircle } from "lucide-react"
import { useEffect } from "react"
import slugify from "slugify"

type FormData = {
  name: string
  slug: string
}

interface CreateOrganizationModalProps {
  open: boolean
  onOpenChange: (open: boolean) => void
}

export function CreateOrganizationModal({ open, onOpenChange }: CreateOrganizationModalProps) {
  const t = useTranslation()
  const { data, setData, errors, post, processing, reset } = useForm<FormData>({
    name: "",
    slug: "",
  })

  const submit = (e: React.FormEvent) => {
    e.preventDefault()

    post(route("onboarding.organization.store"), {
      onSuccess: () => {
        onOpenChange(false)
        reset()
      },
    })
  }

  // Reset form when modal closes
  useEffect(() => {
    if (!open) {
      reset()
    }
  }, [open, reset])

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{t("organizations.create.title")}</DialogTitle>
          <DialogDescription>{t("organizations.create.description")}</DialogDescription>
        </DialogHeader>

        <form onSubmit={submit} className="space-y-6">
          <div className="grid gap-4">
            <div className="grid gap-2">
              <Label htmlFor="name">{t("organizations.create.name_label")}</Label>
              <Input
                id="name"
                type="text"
                name="name"
                placeholder={t("organizations.create.name_placeholder")}
                autoComplete="organization-name"
                value={data.name}
                autoFocus
                onChange={(e) => {
                  setData("name", e.target.value)
                  setData("slug", slugify(e.target.value, { lower: true, trim: true, strict: true }))
                }}
              />
              <InputError message={errors.name} />
            </div>

            <div className="grid gap-2">
              <Label htmlFor="slug">{t("organizations.create.slug_label")}</Label>
              <Input
                id="slug"
                type="text"
                name="slug"
                placeholder={t("organizations.create.slug_placeholder")}
                autoComplete="organization-slug"
                value={data.slug}
                onChange={(e) => setData("slug", e.target.value)}
              />
              <InputError message={errors.slug} />
            </div>
          </div>

          <div className="flex justify-end gap-3">
            <Button type="button" variant="outline" onClick={() => onOpenChange(false)} disabled={processing}>
              {t("ui.actions.cancel")}
            </Button>
            <Button type="submit" disabled={processing}>
              {processing && <LoaderCircle className="mr-2 h-4 w-4 animate-spin" />}
              {t("organizations.create.create_button")}
            </Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  )
}
