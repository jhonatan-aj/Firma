
import requests
import subprocess
import sys
import os


API_URL = "http://127.0.0.1:8000/api"
LANZADOR_SCRIPT = os.path.join(os.path.dirname(__file__), 'lanzador.py')
MANUAL_PDF_PATH = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', 'storage', 'app', 'temp', 'documento.pdf'))

def main():
    
    # Verificar PDF manual
    print(f"1. Buscando PDF manual en: {MANUAL_PDF_PATH}")
    if not os.path.exists(MANUAL_PDF_PATH):
        print(f"   ❌ Error: No se encontró el archivo. Por favor coloca 'documento.pdf' en storage/app/temp/")
        return
    
    print(f"   ✅ Archivo encontrado.")
    pdf_path = MANUAL_PDF_PATH

    # simula el frontend cuando ejecuta el lanzador
    print("\n2. Ejecutando script lanzador (Puente)...")
    cmd = [
        sys.executable, 
        LANZADOR_SCRIPT, 
        '--archivo', pdf_path, 
        '--url', f"{API_URL}/firma/upload-callback"
    ]
    
    print(f"   Comando: {' '.join(cmd)}")
    
    try:
        result = subprocess.run(cmd, capture_output=True, text=True)
        
        print("\n--- RESULTADO DEL LANZADOR ---")
        print(result.stdout)
        
        if result.stderr:
            print("Errores/Logs:")
            print(result.stderr)
            
    except Exception as e:
        print(f"   ❌ Error ejecutando lanzador: {e}")

if __name__ == "__main__":
    main()
