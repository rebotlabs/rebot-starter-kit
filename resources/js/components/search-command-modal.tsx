import { CommandDialog, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList, CommandSeparator } from "@/components/ui/command"
import { useLang } from "@/hooks/useLang"
import type { SharedData } from "@/types"
import { usePage } from "@inertiajs/react"
import { Search, Settings, Users } from "lucide-react"
import { useEffect, useState } from "react"

interface SearchResult {
  id: string
  title: string
  description: string
  type: string
}

interface SearchCommandModalProps {
  open: boolean
  onOpenChange: (open: boolean) => void
}

export function SearchCommandModal({ open, onOpenChange }: SearchCommandModalProps) {
  const { __ } = useLang()
  const { props } = usePage<SharedData>()
  const { currentOrganization } = props

  const [searchQuery, setSearchQuery] = useState("")

  // Reset search when modal closes
  useEffect(() => {
    if (!open) {
      setSearchQuery("")
    }
  }, [open])

  // Quick access items - these will be expanded in the future
  const quickActions = [
    {
      group: __("search.quick_actions"),
      items: [
        {
          id: "members",
          title: __("search.view_members"),
          description: __("search.view_members_description"),
          icon: Users,
          action: () => {
            // Future: Navigate to members page
            console.log("Navigate to members")
            onOpenChange(false)
          },
        },
        {
          id: "settings",
          title: __("search.organization_settings"),
          description: __("search.organization_settings_description"),
          icon: Settings,
          action: () => {
            // Future: Navigate to organization settings
            console.log("Navigate to organization settings")
            onOpenChange(false)
          },
        },
      ],
    },
  ]

  // Future search results - this will be populated with actual search data
  const searchResults: SearchResult[] = [
    // This will be replaced with real search results from the backend
  ]

  const handleSelect = (action: () => void) => {
    action()
  }

  return (
    <CommandDialog open={open} onOpenChange={onOpenChange}>
      <CommandInput
        placeholder={__("search.placeholder", { organization: currentOrganization?.name || "organization" })}
        value={searchQuery}
        onValueChange={setSearchQuery}
      />
      <CommandList>
        {!searchQuery && (
          <>
            {quickActions.map((group) => (
              <CommandGroup key={group.group} heading={group.group}>
                {group.items.map((item) => (
                  <CommandItem key={item.id} value={item.id} onSelect={() => handleSelect(item.action)} className="flex items-center gap-3 p-3">
                    <item.icon className="text-muted-foreground h-4 w-4" />
                    <div className="flex flex-col">
                      <span className="font-medium">{item.title}</span>
                      <span className="text-muted-foreground text-xs">{item.description}</span>
                    </div>
                  </CommandItem>
                ))}
              </CommandGroup>
            ))}
            <CommandSeparator />
            <CommandGroup heading={__("search.recent")}>
              <CommandItem disabled className="text-muted-foreground text-center">
                {__("search.no_recent_searches")}
              </CommandItem>
            </CommandGroup>
          </>
        )}

        {searchQuery && (
          <>
            <CommandEmpty>
              <div className="flex flex-col items-center gap-2 py-6">
                <Search className="text-muted-foreground h-8 w-8" />
                <div className="text-center">
                  <p className="font-medium">{__("search.no_results")}</p>
                  <p className="text-muted-foreground text-sm">{__("search.no_results_description", { query: searchQuery })}</p>
                </div>
              </div>
            </CommandEmpty>

            {/* Future: Real search results will be rendered here */}
            {searchResults.length > 0 && (
              <CommandGroup heading={__("search.results")}>
                {searchResults.map((result: SearchResult) => (
                  <CommandItem key={result.id}>
                    {/* Future: Render actual search result */}
                    {result.title}
                  </CommandItem>
                ))}
              </CommandGroup>
            )}
          </>
        )}
      </CommandList>
    </CommandDialog>
  )
}
