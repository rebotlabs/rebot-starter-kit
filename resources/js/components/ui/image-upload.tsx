import { useTranslation } from "@/hooks/use-i18n"
import { useInitials } from "@/hooks/use-initials"
import { cn } from "@/lib/utils"
import { Camera, Trash2, Upload } from "lucide-react"
import { useRef, useState } from "react"

import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { Button } from "@/components/ui/button"

interface ImageUploadProps {
  currentImage?: string | null
  fallbackText: string
  onImageSelect: (file: File) => void
  onImageRemove: () => void
  isUploading?: boolean
  isRemoving?: boolean
  size?: "sm" | "md" | "lg" | "xl"
  shape?: "circle" | "square"
  uploadText?: string
  changeText?: string
  removeText?: string
  className?: string
}

const sizeClasses = {
  sm: "size-12",
  md: "size-16", 
  lg: "size-20",
  xl: "size-24",
}

const shapeClasses = {
  circle: "rounded-full",
  square: "rounded-lg",
}

export function ImageUpload({
  currentImage,
  fallbackText,
  onImageSelect,
  onImageRemove,
  isUploading = false,
  isRemoving = false,
  size = "lg",
  shape = "circle",
  uploadText,
  changeText,
  removeText,
  className,
}: ImageUploadProps) {
  const t = useTranslation()
  const getInitials = useInitials()
  const fileInputRef = useRef<HTMLInputElement>(null)
  const [dragOver, setDragOver] = useState(false)

  const handleFileSelect = (file: File) => {
    if (file && file.type.startsWith("image/")) {
      onImageSelect(file)
    }
  }

  const handleFileInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0]
    if (file) {
      handleFileSelect(file)
    }
  }

  const handleDragOver = (e: React.DragEvent) => {
    e.preventDefault()
    setDragOver(true)
  }

  const handleDragLeave = (e: React.DragEvent) => {
    e.preventDefault()
    setDragOver(false)
  }

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault()
    setDragOver(false)
    const file = e.dataTransfer.files?.[0]
    if (file) {
      handleFileSelect(file)
    }
  }

  const handleClick = () => {
    fileInputRef.current?.click()
  }

  const initials = getInitials(fallbackText)

  return (
    <div className={cn("flex flex-col items-center space-y-4", className)}>
      <div className="relative">
        <div
          className={cn(
            "relative cursor-pointer transition-all duration-200",
            sizeClasses[size],
            dragOver && "scale-105"
          )}
          onClick={handleClick}
          onDragOver={handleDragOver}
          onDragLeave={handleDragLeave}
          onDrop={handleDrop}
        >
          <Avatar className={cn("size-full", shapeClasses[shape])}>
            {currentImage ? (
              <AvatarImage src={currentImage} alt={fallbackText} />
            ) : null}
            <AvatarFallback className={cn(shapeClasses[shape])}>
              {initials}
            </AvatarFallback>
          </Avatar>

          {/* Overlay on hover */}
          <div className={cn(
            "absolute inset-0 flex items-center justify-center opacity-0 transition-opacity duration-200 hover:opacity-100",
            shapeClasses[shape],
            "bg-black/50"
          )}>
            <Camera className="size-5 text-white" />
          </div>

          {/* Loading overlay */}
          {(isUploading || isRemoving) && (
            <div className={cn(
              "absolute inset-0 flex items-center justify-center",
              shapeClasses[shape],
              "bg-black/50"
            )}>
              <div className="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent" />
            </div>
          )}
        </div>
      </div>

      <div className="flex gap-2">
        <Button
          type="button"
          variant="outline"
          size="sm"
          onClick={handleClick}
          disabled={isUploading || isRemoving}
        >
          <Upload className="mr-2 size-4" />
          {currentImage 
            ? changeText || t("ui.buttons.change")
            : uploadText || t("ui.buttons.upload")
          }
        </Button>

        {currentImage && (
          <Button
            type="button"
            variant="outline"
            size="sm"
            onClick={onImageRemove}
            disabled={isUploading || isRemoving}
          >
            <Trash2 className="mr-2 size-4" />
            {removeText || t("ui.buttons.remove")}
          </Button>
        )}
      </div>

      <input
        ref={fileInputRef}
        type="file"
        accept="image/*"
        onChange={handleFileInputChange}
        className="hidden"
      />
    </div>
  )
}
