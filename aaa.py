#!/usr/bin/env python3
import os
import sys
from pathlib import Path

def should_skip_file(file_path):
    """Arquivos e pastas que devemos ignorar"""
    skip_extensions = {'.pyc', '.pyo', '.pyd', '.exe', '.dll', '.so', '.dylib', 
                      '.jpg', '.jpeg', '.png', '.gif', '.bmp', '.ico', '.pdf',
                      '.zip', '.tar', '.gz', '.rar', '.7z', '.bin', '.dat'}
    
    skip_folders = {'.git', '__pycache__', 'node_modules', '.vscode', '.idea', 
                   'venv', 'env', '.env', 'dist', 'build', '.pytest_cache'}
    
    # Verifica se está em uma pasta que deve ser ignorada
    for part in file_path.parts:
        if part in skip_folders:
            return True
    
    # Verifica extensão
    if file_path.suffix.lower() in skip_extensions:
        return True
        
    return False

def remove_trailing_blank_lines(file_path):
    """Remove linhas em branco do final do arquivo"""
    try:
        with open(file_path, 'rb') as f:
            content = f.read()
        
        # Se arquivo está vazio, não faz nada
        if not content:
            return False
        
        # Remove quebras de linha do final
        original_length = len(content)
        content = content.rstrip(b'\n')
        
        # Se removeu algo, reescreve o arquivo
        if len(content) < original_length:
            with open(file_path, 'wb') as f:
                f.write(content)
            return True
            
        return False
        
    except (PermissionError, UnicodeDecodeError, OSError) as e:
        print(f"Erro ao processar {file_path}: {e}")
        return False

def main():
    current_dir = Path('.')
    modified_files = []
    
    print("🔧 Iniciando limpeza: removendo linhas em branco do final...")
    print("=" * 50)
    
    for file_path in current_dir.rglob('*'):
        if file_path.is_file() and not should_skip_file(file_path):
            if remove_trailing_blank_lines(file_path):
                modified_files.append(str(file_path))
                print(f"✅ Limpo: {file_path}")
    
    print("=" * 50)
    print(f"🎉 Limpeza concluída!")
    print(f"📊 Total de arquivos processados: {len(modified_files)}")
    
    if modified_files:
        print("\n📝 Arquivos que foram limpos:")
        for file in modified_files:
            print(f"   - {file}")
        
        print("\n💡 Agora você pode fazer:")
        print("   git add .")
        print('   git commit -m "Limpeza: remover quebras de linha extras"')
        print("   git push")
    else:
        print("\n📋 Nenhum arquivo precisou ser limpo.")

if __name__ == "__main__":
    main()